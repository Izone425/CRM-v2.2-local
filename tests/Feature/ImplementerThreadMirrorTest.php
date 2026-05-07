<?php

namespace Tests\Feature;

use App\Filament\Actions\ImplementerActions;
use App\Models\Customer;
use App\Models\EmailTemplate;
use App\Models\ImplementerTicket;
use App\Models\ImplementerTicketReply;
use App\Models\Lead;
use App\Models\SoftwareHandover;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ImplementerThreadMirrorTest extends TestCase
{
    use DatabaseTransactions;

    private function makeContext(?EmailTemplate $template = null): array
    {
        $lead       = Lead::factory()->create();
        $handover   = SoftwareHandover::factory()->create(['lead_id' => $lead->id]);
        $customer   = Customer::factory()->create(['lead_id' => $lead->id]);
        $implementer = User::factory()->create();
        $template   = $template ?? EmailTemplate::factory()->create();

        return compact('lead', 'handover', 'customer', 'implementer', 'template');
    }

    /**
     * Acquire the kickoff template at the constant ID — the dev DB already
     * has it seeded; we only tweak thread_label inside the transaction.
     */
    private function kickOffTemplate(): EmailTemplate
    {
        $tpl = EmailTemplate::find(EmailTemplate::KICK_OFF_TEMPLATE_ID);
        if (!$tpl) {
            // Fallback for fresh test DBs without seeded templates.
            $tpl = EmailTemplate::factory()->create([
                'id' => EmailTemplate::KICK_OFF_TEMPLATE_ID,
                'name' => 'Session - Completed Online Kick-Off Meeting',
                'thread_label' => 'Kick-Off Meeting',
                'type' => 'implementer',
            ]);
        }
        $tpl->update(['thread_label' => 'Kick-Off Meeting']);
        return $tpl;
    }

    public function test_kickoff_template_with_no_master_ticket_creates_master_and_first_reply(): void
    {
        $template = $this->kickOffTemplate();
        ['handover' => $h, 'customer' => $c, 'implementer' => $u] = $this->makeContext($template);

        $reply = ImplementerActions::mirrorTemplateEmailToThread(
            $template, $h, $c, $u,
            'Kick-Off Meeting | Acme',
            '<p>Resolved body</p>',
            []
        );

        $this->assertNotNull($reply);
        $master = $reply->ticket;
        $this->assertSame($c->id, $master->customer_id);
        $this->assertSame($h->id, $master->software_handover_id);
        $this->assertSame('Kick-Off Meeting', $master->category);
        $this->assertSame('Kick-Off Meeting | Acme', $master->subject);
        $this->assertNotNull($master->first_responded_at);
        $this->assertMatchesRegularExpression(
            '/^SW_\d{6}_IMP\d{4}$/',
            $master->fresh()->ticket_number
        );
        $this->assertSame($template->id, $reply->email_template_id);
        $this->assertSame('Kick-Off Meeting', $reply->thread_label);
        $this->assertFalse((bool) $reply->is_internal_note);
    }

    public function test_kickoff_template_with_existing_master_appends_no_duplicate(): void
    {
        $template = $this->kickOffTemplate();
        ['handover' => $h, 'customer' => $c, 'implementer' => $u, 'lead' => $l] = $this->makeContext($template);

        $existing = ImplementerTicket::factory()->create([
            'lead_id' => $l->id,
            'software_handover_id' => $h->id,
            'customer_id' => $c->id,
            'implementer_user_id' => $u->id,
        ]);

        $before = ImplementerTicket::where('software_handover_id', $h->id)->count();

        ImplementerActions::mirrorTemplateEmailToThread(
            $template, $h, $c, $u, 'Re-send', '<p>Body</p>', []
        );

        $after = ImplementerTicket::where('software_handover_id', $h->id)->count();
        $this->assertSame($before, $after, 'Should not create a second master ticket');

        $this->assertSame(
            1,
            ImplementerTicketReply::where('implementer_ticket_id', $existing->id)->count()
        );
    }

    public function test_non_kickoff_template_with_no_master_skips_mirror(): void
    {
        $template = EmailTemplate::factory()->create(); // not kickoff
        ['handover' => $h, 'customer' => $c, 'implementer' => $u] = $this->makeContext($template);

        $reply = ImplementerActions::mirrorTemplateEmailToThread(
            $template, $h, $c, $u, 'Follow Up', '<p>Body</p>', []
        );

        $this->assertNull($reply);
        $this->assertSame(0, ImplementerTicket::where('software_handover_id', $h->id)->count());
    }

    public function test_non_kickoff_template_with_master_appends_labeled_reply(): void
    {
        $template = EmailTemplate::factory()->create([
            'name' => 'Follow Up - Data Migration',
            'thread_label' => 'Follow Up-Migration',
        ]);
        ['handover' => $h, 'customer' => $c, 'implementer' => $u, 'lead' => $l] = $this->makeContext($template);

        $master = ImplementerTicket::factory()->create([
            'lead_id' => $l->id,
            'software_handover_id' => $h->id,
            'customer_id' => $c->id,
            'implementer_user_id' => $u->id,
        ]);

        $reply = ImplementerActions::mirrorTemplateEmailToThread(
            $template, $h, $c, $u, 'Subject', '<p>Migration body</p>', ['file1.pdf']
        );

        $this->assertNotNull($reply);
        $this->assertSame($master->id, $reply->implementer_ticket_id);
        $this->assertSame($template->id, $reply->email_template_id);
        $this->assertSame('Follow Up-Migration', $reply->thread_label);
        $this->assertSame(['file1.pdf'], $reply->attachments);
    }

    public function test_template_with_null_thread_label_skips_mirror(): void
    {
        $template = $this->kickOffTemplate();
        $template->update(['thread_label' => null]);
        ['handover' => $h, 'customer' => $c, 'implementer' => $u] = $this->makeContext($template);

        $reply = ImplementerActions::mirrorTemplateEmailToThread(
            $template, $h, $c, $u, 'X', '<p>X</p>', []
        );

        $this->assertNull($reply);
        $this->assertSame(0, ImplementerTicket::where('software_handover_id', $h->id)->count());
    }

    public function test_missing_customer_logs_warning_and_skips(): void
    {
        $template = $this->kickOffTemplate();
        $lead = Lead::factory()->create();
        $h    = SoftwareHandover::factory()->create(['lead_id' => $lead->id]);
        $u    = User::factory()->create();

        $reply = ImplementerActions::mirrorTemplateEmailToThread(
            $template, $h, null, $u, 'X', '<p>X</p>', []
        );

        $this->assertNull($reply);
        $this->assertSame(0, ImplementerTicket::where('software_handover_id', $h->id)->count());
    }
}

<?php

namespace Tests\Feature;

use App\Mail\ImplementerThreadNotificationMail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ImplementerThreadNotificationMailTest extends TestCase
{
    public function test_renders_subject_and_portal_cta(): void
    {
        Mail::fake();

        $portalUrl = 'https://example.com/customer/dashboard?tab=impThread&ticket=42';
        $mailable = new ImplementerThreadNotificationMail(
            emailSubject: 'Follow Up | TimeTec HR | Acme Corp',
            portalUrl: $portalUrl,
            implementerName: 'Jane Implementer',
            implementerDesignation: 'Senior Implementer',
            implementerCompany: 'TimeTec Cloud Sdn Bhd',
            implementerPhone: '03-80709933',
            implementerEmail: 'jane@timetec.test',
            senderEmail: 'jane@timetec.test',
            senderName: 'Jane Implementer',
        );

        $rendered = $mailable->render();

        $this->assertStringContainsString('Follow Up | TimeTec HR | Acme Corp', $mailable->envelope()->subject);
        $this->assertStringContainsString($portalUrl, $rendered);
        $this->assertStringContainsString('View in Customer Portal', $rendered);
        $this->assertStringContainsString('Jane Implementer', $rendered);
        $this->assertStringNotContainsString('<input', $rendered);
    }

    public function test_cta_degrades_to_thread_list_when_no_master_ticket(): void
    {
        $degradedUrl = 'https://example.com/customer/dashboard?tab=impThread';

        $mailable = new ImplementerThreadNotificationMail(
            emailSubject: 'Pre-kickoff follow up',
            portalUrl: $degradedUrl,
            implementerName: 'Jane Implementer',
        );

        $rendered = $mailable->render();

        $this->assertStringContainsString($degradedUrl, $rendered);
        $this->assertStringNotContainsString('ticket=', $rendered);
    }
}

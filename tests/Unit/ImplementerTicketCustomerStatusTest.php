<?php

namespace Tests\Unit;

use App\Enums\ImplementerTicketStatus;
use App\Models\Customer;
use App\Models\ImplementerTicket;
use App\Models\ImplementerTicketReply;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ImplementerTicketCustomerStatusTest extends TestCase
{
    private function makeTicket(ImplementerTicketStatus $status, ?Collection $replies = null): ImplementerTicket
    {
        $ticket = new ImplementerTicket();
        $ticket->status = $status;
        if ($replies !== null) {
            $ticket->setRelation('replies', $replies);
        }
        return $ticket;
    }

    private function makeReply(string $senderType, Carbon $createdAt, bool $internal = false): ImplementerTicketReply
    {
        $reply = new ImplementerTicketReply();
        $reply->sender_type = $senderType;
        $reply->is_internal_note = $internal;
        $reply->created_at = $createdAt;
        return $reply;
    }

    public function test_closed_status_returns_closed(): void
    {
        $ticket = $this->makeTicket(ImplementerTicketStatus::CLOSED, collect());
        $this->assertSame('closed', $ticket->customerFacingStatus());
    }

    public function test_pending_support_returns_in_progress(): void
    {
        $ticket = $this->makeTicket(ImplementerTicketStatus::PENDING_SUPPORT, collect());
        $this->assertSame('in_progress', $ticket->customerFacingStatus());
    }

    public function test_pending_rnd_returns_in_progress(): void
    {
        $ticket = $this->makeTicket(ImplementerTicketStatus::PENDING_RND, collect());
        $this->assertSame('in_progress', $ticket->customerFacingStatus());
    }

    public function test_pending_client_returns_open(): void
    {
        $ticket = $this->makeTicket(ImplementerTicketStatus::PENDING_CLIENT, collect());
        $this->assertSame('open', $ticket->customerFacingStatus());
    }

    public function test_open_with_no_replies_returns_awaiting_reply(): void
    {
        $ticket = $this->makeTicket(ImplementerTicketStatus::OPEN, collect());
        $this->assertSame('awaiting_reply', $ticket->customerFacingStatus());
    }

    public function test_open_with_last_reply_from_customer_returns_awaiting_reply(): void
    {
        $replies = collect([
            $this->makeReply(User::class, Carbon::parse('2026-05-10 09:00:00')),
            $this->makeReply(Customer::class, Carbon::parse('2026-05-12 14:30:00')),
        ]);
        $ticket = $this->makeTicket(ImplementerTicketStatus::OPEN, $replies);
        $this->assertSame('awaiting_reply', $ticket->customerFacingStatus());
    }

    public function test_open_with_last_reply_from_implementer_returns_open(): void
    {
        $replies = collect([
            $this->makeReply(Customer::class, Carbon::parse('2026-05-10 09:00:00')),
            $this->makeReply(User::class, Carbon::parse('2026-05-12 14:30:00')),
        ]);
        $ticket = $this->makeTicket(ImplementerTicketStatus::OPEN, $replies);
        $this->assertSame('open', $ticket->customerFacingStatus());
    }

    public function test_internal_notes_are_ignored_when_deriving_status(): void
    {
        $replies = collect([
            $this->makeReply(Customer::class, Carbon::parse('2026-05-10 09:00:00')),
            $this->makeReply(User::class, Carbon::parse('2026-05-13 10:00:00'), internal: true),
        ]);
        $ticket = $this->makeTicket(ImplementerTicketStatus::OPEN, $replies);
        $this->assertSame('awaiting_reply', $ticket->customerFacingStatus());
    }

    public function test_derivation_works_when_replies_collection_is_unordered(): void
    {
        $replies = collect([
            $this->makeReply(User::class, Carbon::parse('2026-05-15 11:00:00')),
            $this->makeReply(Customer::class, Carbon::parse('2026-05-10 09:00:00')),
            $this->makeReply(Customer::class, Carbon::parse('2026-05-12 14:30:00')),
        ]);
        $ticket = $this->makeTicket(ImplementerTicketStatus::OPEN, $replies);
        $this->assertSame('open', $ticket->customerFacingStatus());
    }
}

<?php

namespace App\Console\Commands;

use App\Enums\ImplementerTicketStatus;
use App\Models\ImplementerTicket;
use App\Models\SlaConfiguration;
use Illuminate\Console\Command;

class ProcessSlaFirstReplyDeadline extends Command
{
    protected $signature = 'sla:check-first-reply';
    protected $description = 'Check tickets for first reply deadline violations and mark as overdue';

    public function handle(): int
    {
        $config = SlaConfiguration::current();

        if (!$config->first_reply_enabled) {
            $this->info('First reply deadline check is disabled.');
            return 0;
        }

        // Find tickets that have no reply and are not already flagged overdue
        $tickets = ImplementerTicket::where('status', '!=', ImplementerTicketStatus::CLOSED->value)
            ->whereNull('first_responded_at')
            ->where('is_overdue', false)
            ->get();

        $flaggedCount = 0;

        foreach ($tickets as $ticket) {
            $deadline = $ticket->getFirstReplyDeadline();
            if ($deadline && now()->gt($deadline)) {
                $ticket->is_overdue = true;
                $ticket->save();
                $flaggedCount++;
            }
        }

        $this->info("Checked {$tickets->count()} tickets. Flagged {$flaggedCount} as overdue.");
        return 0;
    }
}

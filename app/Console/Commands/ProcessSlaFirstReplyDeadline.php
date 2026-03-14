<?php

namespace App\Console\Commands;

use App\Enums\ImplementerTicketStatus;
use App\Models\ImplementerTicket;
use App\Models\SlaConfiguration;
use Carbon\Carbon;
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

        $cutoffTime = $config->first_reply_cutoff_time; // e.g. '17:30'

        // Find tickets that have no reply and are not already flagged overdue
        $tickets = ImplementerTicket::where('status', '!=', ImplementerTicketStatus::CLOSED->value)
            ->whereNull('first_responded_at')
            ->where('is_overdue', false)
            ->get();

        $flaggedCount = 0;

        foreach ($tickets as $ticket) {
            $createdAt = $ticket->created_at;
            $createdTime = $createdAt->format('H:i');

            if ($createdTime <= $cutoffTime) {
                // Ticket created before cutoff — must reply by 23:59 same day
                $deadline = $createdAt->copy()->endOfDay();

                if (now()->gt($deadline)) {
                    // Check if creation day was a working day
                    if ($config->isWorkingDay($createdAt)) {
                        $ticket->is_overdue = true;
                        $ticket->save();
                        $flaggedCount++;
                    } else {
                        // Created on non-working day — deadline is end of next working day
                        $nextWorkingDay = $config->addWorkingDays($createdAt, 1);
                        if (now()->gt($nextWorkingDay->endOfDay())) {
                            $ticket->is_overdue = true;
                            $ticket->save();
                            $flaggedCount++;
                        }
                    }
                }
            } else {
                // Ticket created after cutoff — deadline is end of next working day
                $nextWorkingDay = $config->addWorkingDays($createdAt, 1);
                $deadline = $nextWorkingDay->endOfDay();

                if (now()->gt($deadline)) {
                    $ticket->is_overdue = true;
                    $ticket->save();
                    $flaggedCount++;
                }
            }
        }

        $this->info("Checked {$tickets->count()} tickets. Flagged {$flaggedCount} as overdue.");
        return 0;
    }
}

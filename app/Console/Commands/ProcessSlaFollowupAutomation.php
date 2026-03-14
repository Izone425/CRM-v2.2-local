<?php

namespace App\Console\Commands;

use App\Enums\ImplementerTicketStatus;
use App\Models\ImplementerTicket;
use App\Models\SlaConfiguration;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessSlaFollowupAutomation extends Command
{
    protected $signature = 'sla:process-followups';
    protected $description = 'Process follow-up reminders and auto-close for pending client tickets';

    public function handle(): int
    {
        $config = SlaConfiguration::current();

        if (!$config->followup_enabled) {
            $this->info('Follow-up automation is disabled.');
            return 0;
        }

        // Find all pending_client tickets with pending_client_since set
        $tickets = ImplementerTicket::where('status', ImplementerTicketStatus::PENDING_CLIENT->value)
            ->whereNotNull('pending_client_since')
            ->get();

        if ($tickets->isEmpty()) {
            $this->info('No pending client tickets to process.');
            return 0;
        }

        // Pre-fetch holidays for the date range to optimize working-day calculation
        $earliestDate = $tickets->min('pending_client_since');
        $holidays = SlaConfiguration::fetchHolidaysForRange(
            Carbon::parse($earliestDate)->toDateString(),
            now()->toDateString()
        );

        $reminderCount = 0;
        $closedCount = 0;
        $totalDaysThreshold = $config->followup_reminder_days + $config->followup_auto_close_days;

        foreach ($tickets as $ticket) {
            $workingDays = SlaConfiguration::countWorkingDaysWithHolidays(
                $ticket->pending_client_since,
                now(),
                $holidays
            );

            // Auto-close threshold (check first since it's the higher threshold)
            if ($workingDays >= $totalDaysThreshold) {
                $ticket->status = ImplementerTicketStatus::CLOSED;
                $ticket->closed_at = now();
                $ticket->closed_by = null;
                $ticket->closed_by_type = 'system';
                $ticket->save();
                $closedCount++;
                continue;
            }

            // Follow-up reminder threshold
            if ($workingDays >= $config->followup_reminder_days && !$ticket->followup_sent_at) {
                $ticket->followup_sent_at = now();
                $ticket->save();
                $reminderCount++;
            }
        }

        $this->info("Processed {$tickets->count()} tickets. Reminders: {$reminderCount}. Auto-closed: {$closedCount}.");
        return 0;
    }
}

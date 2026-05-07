<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendScheduledEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send emails that have been scheduled for delivery today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $this->info("Starting scheduled email processing at {$now}");

        // Get today's scheduled emails with "New" status
        $scheduledEmails = DB::table('scheduled_emails')
            ->whereDate('scheduled_date', $today)
            ->where('status', 'New')
            ->get();

        $count = $scheduledEmails->count();
        $this->info("Found {$count} emails scheduled for today");

        foreach ($scheduledEmails as $email) {
            try {
                $emailData = json_decode($email->email_data, true);

                if (!$emailData) {
                    $this->error("Could not parse email data for ID {$email->id}");
                    continue;
                }

                // Get CC recipients (if stored in email data)
                $ccRecipients = $emailData['cc_recipients'] ?? [];

                // Pre-resolve master ticket id for the CTA URL (re-checked at send-time
                // so a master created since the email was scheduled is reflected).
                $masterTicketId = null;
                if (!empty($emailData['software_handover_id'])) {
                    $existingMaster = \App\Models\ImplementerTicket::where('software_handover_id', $emailData['software_handover_id'])
                        ->orderBy('id', 'asc')
                        ->first();
                    $masterTicketId = $existingMaster?->id;
                }

                $portalBase = str_replace('http://', 'https://', config('app.url'));
                $portalUrl  = $portalBase . '/customer/dashboard?tab=impThread'
                    . ($masterTicketId ? '&ticket=' . $masterTicketId : '');

                $mailable = new \App\Mail\ImplementerThreadNotificationMail(
                    emailSubject:           $emailData['subject'] ?? 'Customer portal update',
                    portalUrl:              $portalUrl,
                    implementerName:        $emailData['sender_name'] ?? '',
                    implementerDesignation: $emailData['implementer_designation'] ?? 'Implementer',
                    implementerCompany:     $emailData['implementer_company'] ?? 'TimeTec Cloud Sdn Bhd',
                    implementerPhone:       $emailData['implementer_phone'] ?? '',
                    implementerEmail:       $emailData['implementer_email'] ?? '',
                    senderEmail:            $emailData['sender_email'] ?? '',
                    senderName:             $emailData['sender_name'] ?? '',
                );

                $mailBuilder = Mail::to($emailData['recipients'] ?? []);

                if (!empty($ccRecipients)) {
                    $mailBuilder->cc($ccRecipients);
                }
                if (!empty($emailData['sender_email'])) {
                    $mailBuilder->bcc($emailData['sender_email']);
                }

                $mailBuilder->send($mailable);

                // === Mirror to customer thread (template-driven) ===
                try {
                    if (!empty($emailData['template_id'])) {
                        $template = \App\Models\EmailTemplate::find($emailData['template_id']);
                        $handover = !empty($emailData['software_handover_id'])
                            ? \App\Models\SoftwareHandover::find($emailData['software_handover_id'])
                            : null;
                        $customer = !empty($emailData['customer_id'])
                            ? \App\Models\Customer::find($emailData['customer_id'])
                            : null;
                        $implementer = !empty($emailData['implementer_user_id'])
                            ? \App\Models\User::find($emailData['implementer_user_id'])
                            : null;

                        if ($template && $implementer) {
                            \App\Filament\Actions\ImplementerActions::mirrorTemplateEmailToThread(
                                $template,
                                $handover,
                                $customer,
                                $implementer,
                                $emailData['subject'] ?? '',
                                $emailData['content'] ?? '',
                                $emailData['project_plan_attachments'] ?? []
                            );
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error('Thread mirror failed in SendScheduledEmails: ' . $e->getMessage(), [
                        'scheduled_email_id' => $email->id,
                        'template_id'        => $emailData['template_id'] ?? null,
                    ]);
                }

                // Mark the email as sent
                DB::table('scheduled_emails')
                    ->where('id', $email->id)
                    ->update([
                        'status' => 'Done',
                        'sent_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                $this->info("Email ID {$email->id} sent successfully");

                // Log successful send
                Log::info('Scheduled email sent', [
                    'email_id' => $email->id,
                    'recipients' => $emailData['recipients'],
                    'cc_recipients' => $ccRecipients,
                    'subject' => $emailData['subject'],
                    'master_ticket_id' => $masterTicketId,
                    'template' => $emailData['template_name'] ?? 'Unknown',
                ]);

                // Sleep briefly to prevent flooding the mail server
                usleep(500000); // 0.5 seconds

            } catch (\Exception $e) {
                $this->error("Failed to send email ID {$email->id}: {$e->getMessage()}");

                // Mark as failed
                DB::table('scheduled_emails')
                    ->where('id', $email->id)
                    ->update([
                        'status' => 'Failed',
                        'error_message' => $e->getMessage(),
                        'updated_at' => Carbon::now(),
                    ]);

                Log::error('Failed to send scheduled email', [
                    'email_id' => $email->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $this->info('Email scheduling task completed');
        return 0;
    }
}

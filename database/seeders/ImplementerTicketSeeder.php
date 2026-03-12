<?php

namespace Database\Seeders;

use App\Enums\ImplementerTicketStatus;
use App\Models\Customer;
use App\Models\ImplementerTicket;
use App\Models\ImplementerTicketReply;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ImplementerTicketSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing customers and users
        $customers = Customer::whereNotNull('company_name')
            ->where('company_name', '!=', '')
            ->inRandomOrder()
            ->take(8)
            ->get();

        $users = User::take(4)->get();

        if ($customers->count() < 3 || $users->count() < 1) {
            $this->command->error('Not enough customers or users in the database. Need at least 3 customers and 1 user.');
            return;
        }

        $categories = [
            'License Activation',
            'Data Migration',
            'Software Enquiries',
            'Session Enquiries',
            'Training Enquiries',
            'Enhancement/CR',
            'Add-on License',
            'Others',
        ];

        $now = Carbon::now();

        // Define 15 tickets with specific SLA scenarios
        $ticketDefinitions = [
            // --- 3 OPEN tickets ---
            [
                'status' => 'open',
                'priority' => 'high',
                'category' => 'Software Enquiries',
                'subject' => 'Payroll calculation showing incorrect overtime rates',
                'description' => 'The overtime calculation in the payroll module is not reflecting the updated rates we configured last week. Employees are receiving incorrect overtime pay.',
                'created_hours_ago' => 8, // on track
                'first_responded_hours_after' => null,
                'closed' => false,
            ],
            [
                'status' => 'open',
                'priority' => 'medium',
                'category' => 'Session Enquiries',
                'subject' => 'Unable to access leave management module',
                'description' => 'Several HR staff members are unable to access the leave management module after the recent system update. They see a blank page when navigating to the module.',
                'created_hours_ago' => 20, // on track
                'first_responded_hours_after' => null,
                'closed' => false,
            ],
            [
                'status' => 'open',
                'priority' => 'low',
                'category' => 'Training Enquiries',
                'subject' => 'Request for attendance system training for new staff',
                'description' => 'We have 5 new HR staff members who need training on the attendance system. Please schedule a training session at your earliest convenience.',
                'created_hours_ago' => 4, // on track
                'first_responded_hours_after' => null,
                'closed' => false,
            ],

            // --- 3 PENDING_SUPPORT tickets ---
            [
                'status' => 'pending_support',
                'priority' => 'high',
                'category' => 'Data Migration',
                'subject' => 'Employee records missing after data migration',
                'description' => 'After migrating from the old system, approximately 50 employee records are missing from the database. We need these restored urgently as payroll is due next week.',
                'created_hours_ago' => 52, // overdue!
                'first_responded_hours_after' => 18,
                'closed' => false,
            ],
            [
                'status' => 'pending_support',
                'priority' => 'medium',
                'category' => 'Software Enquiries',
                'subject' => 'Report generation taking too long',
                'description' => 'Monthly attendance reports that used to generate in seconds now take over 5 minutes. This is affecting our operations.',
                'created_hours_ago' => 40, // at risk
                'first_responded_hours_after' => 12,
                'closed' => false,
            ],
            [
                'status' => 'pending_support',
                'priority' => 'low',
                'category' => 'Enhancement/CR',
                'subject' => 'Request to add custom fields in employee profile',
                'description' => 'We would like to add custom fields for employee certifications and professional memberships in the profile module.',
                'created_hours_ago' => 16, // on track
                'first_responded_hours_after' => 10,
                'closed' => false,
            ],

            // --- 3 PENDING_CLIENT tickets ---
            [
                'status' => 'pending_client',
                'priority' => 'high',
                'category' => 'License Activation',
                'subject' => 'License activation failed for new module',
                'description' => 'We purchased the appraisal module license but activation is showing an error code LA-4052. Please assist.',
                'created_hours_ago' => 60, // overdue!
                'first_responded_hours_after' => 6,
                'closed' => false,
            ],
            [
                'status' => 'pending_client',
                'priority' => 'medium',
                'category' => 'Software Enquiries',
                'subject' => 'Claim submission form not saving attachments',
                'description' => 'When employees submit claims with receipt attachments, the attachments are not being saved. The claim is submitted but without supporting documents.',
                'created_hours_ago' => 38, // at risk
                'first_responded_hours_after' => 20,
                'closed' => false,
            ],
            [
                'status' => 'pending_client',
                'priority' => 'low',
                'category' => 'Others',
                'subject' => 'Inquiry about system backup schedule',
                'description' => 'We would like to know the current backup schedule and data retention policy for our account.',
                'created_hours_ago' => 10, // on track
                'first_responded_hours_after' => 5,
                'closed' => false,
            ],

            // --- 2 PENDING_RND tickets ---
            [
                'status' => 'pending_rnd',
                'priority' => 'high',
                'category' => 'Software Enquiries',
                'subject' => 'Critical bug in attendance biometric integration',
                'description' => 'The biometric device integration is dropping connections intermittently, causing missed clock-in/clock-out records for approximately 30% of employees.',
                'created_hours_ago' => 30, // on track but escalated
                'first_responded_hours_after' => 4,
                'closed' => false,
            ],
            [
                'status' => 'pending_rnd',
                'priority' => 'medium',
                'category' => 'Data Migration',
                'subject' => 'Data format incompatibility with legacy system export',
                'description' => 'The CSV export from our legacy system has date formats that are not being parsed correctly during import. Need R&D to update the parser.',
                'created_hours_ago' => 26, // on track
                'first_responded_hours_after' => 22,
                'closed' => false,
            ],

            // --- 4 CLOSED tickets ---
            [
                'status' => 'closed',
                'priority' => 'medium',
                'category' => 'License Activation',
                'subject' => 'License renewal for payroll module',
                'description' => 'Our payroll module license expired yesterday. Please process the renewal as payroll processing is due this Friday.',
                'created_hours_ago' => 168, // 7 days ago
                'first_responded_hours_after' => 2,
                'closed' => true,
                'closed_hours_after' => 24, // within SLA
            ],
            [
                'status' => 'closed',
                'priority' => 'high',
                'category' => 'Software Enquiries',
                'subject' => 'Login error after password reset',
                'description' => 'Multiple users unable to login after performing password reset. Getting "Invalid credentials" error even with correct new password.',
                'created_hours_ago' => 240, // 10 days ago
                'first_responded_hours_after' => 1,
                'closed' => true,
                'closed_hours_after' => 8, // within SLA
            ],
            [
                'status' => 'closed',
                'priority' => 'low',
                'category' => 'Add-on License',
                'subject' => 'Add-on module activation for hiring workflow',
                'description' => 'We have purchased the hiring workflow add-on. Please activate it for our account.',
                'created_hours_ago' => 336, // 14 days ago
                'first_responded_hours_after' => 16,
                'closed' => true,
                'closed_hours_after' => 36, // within SLA
            ],
            [
                'status' => 'closed',
                'priority' => 'medium',
                'category' => 'Session Enquiries',
                'subject' => 'Session timeout too short for data entry',
                'description' => 'The session timeout is set to 15 minutes which is too short. HR staff lose their work when entering lengthy employee data.',
                'created_hours_ago' => 200, // ~8 days ago
                'first_responded_hours_after' => 28, // outside first response SLA
                'closed' => true,
                'closed_hours_after' => 72, // outside resolution SLA!
            ],
        ];

        // Customer/implementer messages for replies
        $customerMessages = [
            'Thank you for looking into this. When can we expect a resolution?',
            'This is causing significant disruption to our operations. Please prioritize.',
            'We have attached additional screenshots showing the issue.',
            'Is there a workaround we can use in the meantime?',
            'Our team needs this resolved before end of business today.',
            'We noticed the issue is happening across all departments, not just HR.',
            'Please update us on the progress. Our management is asking for a timeline.',
            'The issue seems to occur only during peak hours (9 AM - 11 AM).',
        ];

        $implementerMessages = [
            'Thank you for reporting this issue. I am currently investigating and will update you shortly.',
            'I have identified the root cause and am working on a fix. Expected resolution within 4 hours.',
            'I have applied a configuration change that should resolve the issue. Please verify on your end.',
            'This requires further investigation. I am escalating to our R&D team for deeper analysis.',
            'The fix has been deployed. Please clear your browser cache and try again.',
            'I have reviewed your request and will need some additional information. Could you provide the affected employee IDs?',
            'Good news - the issue has been resolved. The system should now be functioning correctly.',
            'I have scheduled the training session for next Tuesday at 10 AM. Please confirm if this works for your team.',
        ];

        $internalNotes = [
            'Checked the server logs - seeing database connection timeouts during peak hours. May need to increase connection pool.',
            'Customer has been experiencing this issue for 3 days. Need to prioritize resolution.',
            'Escalated to R&D. The biometric SDK needs an update to support the latest firmware version.',
            'Discussed with team lead - this is a known issue affecting multiple clients. Patch is in progress.',
            'Customer seems satisfied with the workaround for now. Will follow up after the permanent fix is deployed.',
        ];

        $createdTickets = [];

        foreach ($ticketDefinitions as $index => $def) {
            $customer = $customers[$index % $customers->count()];
            $implementer = $users[$index % $users->count()];
            $createdAt = $now->copy()->subHours($def['created_hours_ago']);

            $ticketData = [
                'customer_id' => $customer->id,
                'implementer_user_id' => $implementer->id,
                'implementer_name' => $implementer->name,
                'subject' => $def['subject'],
                'description' => $def['description'],
                'status' => $def['status'],
                'priority' => $def['priority'],
                'category' => $def['category'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            if ($def['first_responded_hours_after']) {
                $ticketData['first_responded_at'] = $createdAt->copy()->addHours($def['first_responded_hours_after']);
            }

            if ($def['closed'] ?? false) {
                $closedAt = $createdAt->copy()->addHours($def['closed_hours_after']);
                $ticketData['closed_at'] = $closedAt;
                $ticketData['closed_by'] = $implementer->id;
                $ticketData['closed_by_type'] = 'user';
            }

            $ticket = ImplementerTicket::create($ticketData);
            $createdTickets[] = [
                'ticket' => $ticket,
                'customer' => $customer,
                'implementer' => $implementer,
                'def' => $def,
            ];
        }

        // Seed replies for tickets
        $replyIndex = 0;
        foreach ($createdTickets as $entry) {
            $ticket = $entry['ticket'];
            $customer = $entry['customer'];
            $implementer = $entry['implementer'];
            $def = $entry['def'];
            $ticketCreatedAt = $ticket->created_at;

            // Determine number of replies based on status
            $replyCount = match ($def['status']) {
                'closed' => rand(3, 5),
                'pending_client', 'pending_rnd' => rand(2, 4),
                'pending_support' => rand(1, 3),
                'open' => rand(0, 1),
                default => 1,
            };

            for ($r = 0; $r < $replyCount; $r++) {
                $hoursAfterCreation = ($r + 1) * rand(2, 8);
                $replyAt = $ticketCreatedAt->copy()->addHours($hoursAfterCreation);

                // Don't create replies in the future
                if ($replyAt->gt($now)) {
                    break;
                }

                // Alternate between customer and implementer
                $isImplementer = ($r % 2 === 0);

                // For pending_client, last reply should be from implementer
                if ($def['status'] === 'pending_client' && $r === $replyCount - 1) {
                    $isImplementer = true;
                }
                // For pending_support, last reply should be from customer
                if ($def['status'] === 'pending_support' && $r === $replyCount - 1) {
                    $isImplementer = false;
                }

                $message = $isImplementer
                    ? $implementerMessages[$replyIndex % count($implementerMessages)]
                    : $customerMessages[$replyIndex % count($customerMessages)];

                ImplementerTicketReply::create([
                    'implementer_ticket_id' => $ticket->id,
                    'sender_type' => $isImplementer ? 'App\Models\User' : 'App\Models\Customer',
                    'sender_id' => $isImplementer ? $implementer->id : $customer->id,
                    'message' => $message,
                    'is_internal_note' => false,
                    'created_at' => $replyAt,
                    'updated_at' => $replyAt,
                ]);

                $replyIndex++;
            }

            // Add internal notes for some tickets
            if (in_array($def['status'], ['pending_rnd', 'pending_support', 'closed']) && rand(0, 1)) {
                $noteAt = $ticketCreatedAt->copy()->addHours(rand(3, 12));
                if ($noteAt->lt($now)) {
                    ImplementerTicketReply::create([
                        'implementer_ticket_id' => $ticket->id,
                        'sender_type' => 'App\Models\User',
                        'sender_id' => $implementer->id,
                        'message' => $internalNotes[$replyIndex % count($internalNotes)],
                        'is_internal_note' => true,
                        'created_at' => $noteAt,
                        'updated_at' => $noteAt,
                    ]);
                    $replyIndex++;
                }
            }
        }

        $totalTickets = ImplementerTicket::count();
        $totalReplies = ImplementerTicketReply::count();
        $this->command->info("Seeded {$totalTickets} implementer tickets with {$totalReplies} replies.");
    }
}

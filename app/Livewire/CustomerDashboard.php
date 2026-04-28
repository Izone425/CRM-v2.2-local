<?php

namespace App\Livewire;

use App\Enums\ImplementerTicketStatus;
use App\Models\Customer;
use App\Models\CustomerDataMigrationFile;
use App\Models\ImplementerAppointment;
use App\Models\ImplementerTicket;
use App\Models\SoftwareHandover;
use App\Models\SoftwareHandoverProcessFile;
use App\Services\ProjectProgressService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomerDashboard extends Component
{
    public string $journeyStage = 'pre_kickoff';
    public string $stageCaption = '';
    public string $greetingTime = '';
    public string $customerName = '';

    public array $journeyNodes = [];
    public ?array $heroCompanion = null;

    public array $actionItems = [];
    public int $actionItemsTotal = 0;

    public array $progressSummary = [];
    public bool $hasProjectPlan = false;

    public array $tickets = [];
    public int $ticketsTotal = 0;
    public string $ticketsHealthState = 'on_track';

    public array $migrationCounts = [
        'pending' => 0,
        'submitted' => 0,
        'approved' => 0,
        'rejected' => 0,
        'total' => 0,
        'completed' => 0,
        'percent' => 0,
    ];

    public array $recentActivity = [];
    public int $unreadNotifications = 0;

    public array $resources = [];
    public array $quickActions = [];

    public function mount(): void
    {
        $this->loadAll();
    }

    public function refresh(): void
    {
        $this->loadAll();
    }

    public function markActivityRead(string $notificationId): void
    {
        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            return;
        }
        $notification = $customer->notifications()->where('id', $notificationId)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        $this->loadActivity($customer);
    }

    public function openActivity(string $notificationId)
    {
        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            return null;
        }
        $notification = $customer->notifications()->where('id', $notificationId)->first();
        if (! $notification) {
            return null;
        }
        $notification->markAsRead();
        $entityId = $notification->data['entity_id'] ?? null;
        if ($entityId) {
            return $this->redirect('/customer/dashboard?tab=impThread&ticket=' . $entityId);
        }
        $this->loadActivity($customer);
        return null;
    }

    public function markAllActivityRead(): void
    {
        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            return;
        }
        $customer->unreadNotifications->markAsRead();
        $this->loadActivity($customer);
    }

    private function loadAll(): void
    {
        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            return;
        }

        $this->customerName = trim(strtok($customer->name ?? '', ' ')) ?: ($customer->name ?? 'there');
        $this->greetingTime = $this->computeGreeting();

        $leadId = $customer->lead_id;
        $handover = $leadId
            ? SoftwareHandover::where('lead_id', $leadId)->orderBy('id', 'desc')->first()
            : null;

        $this->loadProgress($leadId);
        $this->journeyStage = $this->computeStage($leadId, $handover);
        $this->stageCaption = $this->captionForStage($this->journeyStage);

        $this->loadJourneyNodes($handover);
        $this->loadHeroCompanion($leadId, $handover);
        $this->loadActionItems($customer, $leadId);
        $this->loadTickets($leadId);
        $this->loadMigration($leadId);
        $this->loadActivity($customer);
        $this->loadResources($leadId);
        $this->loadQuickActions();
    }

    private function computeGreeting(): string
    {
        $hour = (int) now()->format('H');
        if ($hour < 12) {
            return 'Good morning';
        }
        if ($hour < 18) {
            return 'Good afternoon';
        }
        return 'Good evening';
    }

    private function computeStage(?int $leadId, ?SoftwareHandover $handover): string
    {
        if (! $leadId || ! $handover) {
            return 'pre_kickoff';
        }

        if ($handover->completed_at) {
            $daysSince = Carbon::parse($handover->completed_at)->diffInDays(now());
            return $daysSince > 30 ? 'support_only' : 'live';
        }

        if ($handover->go_live_date && Carbon::parse($handover->go_live_date)->isPast()) {
            return 'live';
        }

        $kickoffComplete = ImplementerAppointment::where('lead_id', $leadId)
            ->where('session', 'Session 1')
            ->where('status', 'Completed')
            ->exists();

        if (! $kickoffComplete) {
            return 'pre_kickoff';
        }

        $overall = $this->progressSummary['overallProgress'] ?? 0;

        if ($handover->go_live_date) {
            $daysToGoLive = now()->diffInDays(Carbon::parse($handover->go_live_date), false);
            if ($overall >= 70 && $daysToGoLive >= 0 && $daysToGoLive <= 30) {
                return 'pre_go_live';
            }
        }

        if ($handover->webinar_training) {
            $diff = now()->diffInDays(Carbon::parse($handover->webinar_training), false);
            if (abs($diff) <= 14) {
                return 'training';
            }
        }

        $hasMigrationActivity = CustomerDataMigrationFile::where('lead_id', $leadId)->exists();
        if ($hasMigrationActivity || $overall >= 25) {
            return 'data_migration';
        }

        return 'kickoff_done';
    }

    private function captionForStage(string $stage): string
    {
        return match ($stage) {
            'pre_kickoff' => 'Welcome aboard — your kick-off session is being prepared.',
            'kickoff_done' => 'Kick-off done. Let\'s start moving on data migration.',
            'data_migration' => 'Data migration in progress.',
            'training' => 'Training is up next — make sure your team is ready.',
            'pre_go_live' => 'Almost there — go-live is around the corner.',
            'live' => 'You\'re live. Welcome to the family.',
            'support_only' => 'You\'re live and supported. Reach out anytime.',
            default => '',
        };
    }

    private function loadProgress(?int $leadId): void
    {
        if (! $leadId) {
            $this->progressSummary = ['overallProgress' => 0, 'modules' => [], 'totalTasks' => 0, 'completedTasks' => 0];
            $this->hasProjectPlan = false;
            return;
        }

        $data = ProjectProgressService::getProjectProgressData($leadId);
        $this->hasProjectPlan = ! empty($data['progressOverview']);
        $this->progressSummary = [
            'overallProgress' => $data['overallSummary']['overallProgress'] ?? 0,
            'totalTasks' => $data['overallSummary']['totalTasks'] ?? 0,
            'completedTasks' => $data['overallSummary']['completedTasks'] ?? 0,
            'modules' => collect($data['overallSummary']['modules'] ?? [])
                ->take(6)
                ->map(fn ($m) => [
                    'name' => $m['module_name'],
                    'progress' => $m['progress'],
                    'completed' => $m['completed'],
                    'total' => $m['total'],
                ])
                ->values()
                ->toArray(),
        ];
    }

    private function loadJourneyNodes(?SoftwareHandover $handover): void
    {
        $stages = [
            ['key' => 'pre_kickoff', 'label' => 'Pre-Kickoff', 'icon' => 'fa-flag'],
            ['key' => 'kickoff', 'label' => 'Kick-off', 'icon' => 'fa-handshake'],
            ['key' => 'data_migration', 'label' => 'Data Migration', 'icon' => 'fa-database'],
            ['key' => 'training', 'label' => 'Training', 'icon' => 'fa-chalkboard-user'],
            ['key' => 'pre_go_live', 'label' => 'Go-Live Prep', 'icon' => 'fa-rocket'],
            ['key' => 'live', 'label' => 'Live', 'icon' => 'fa-circle-check'],
        ];

        $deepLinks = [
            'pre_kickoff' => '?tab=calendar',
            'kickoff' => '?tab=calendar',
            'data_migration' => '?tab=dataMigration',
            'training' => '?tab=webinar',
            'pre_go_live' => '?tab=project',
            'live' => '?tab=impThread',
        ];

        $stageOrder = ['pre_kickoff', 'kickoff_done', 'data_migration', 'training', 'pre_go_live', 'live', 'support_only'];
        $currentIndex = array_search($this->journeyStage, $stageOrder, true);
        if ($currentIndex === false) {
            $currentIndex = 0;
        }
        $nodeIndexForCurrentStage = match ($this->journeyStage) {
            'pre_kickoff' => 0,
            'kickoff_done' => 1,
            'data_migration' => 2,
            'training' => 3,
            'pre_go_live' => 4,
            'live', 'support_only' => 5,
            default => 0,
        };

        $datesByNode = [
            0 => null,
            1 => optional($handover?->kick_off_meeting)->format('d M'),
            2 => null,
            3 => optional($handover?->webinar_training)->format('d M'),
            4 => optional($handover?->go_live_date)->format('d M'),
            5 => optional($handover?->completed_at)->format('d M'),
        ];

        $nodes = [];
        foreach ($stages as $i => $stage) {
            if ($i < $nodeIndexForCurrentStage) {
                $status = 'done';
            } elseif ($i === $nodeIndexForCurrentStage) {
                $status = 'current';
            } else {
                $status = 'upcoming';
            }

            $nodes[] = [
                'key' => $stage['key'],
                'label' => $stage['label'],
                'icon' => $stage['icon'],
                'status' => $status,
                'date' => $datesByNode[$i] ?? null,
                'deepLink' => $deepLinks[$stage['key']] ?? '?tab=calendar',
            ];
        }
        $this->journeyNodes = $nodes;
    }

    private function loadHeroCompanion(?int $leadId, ?SoftwareHandover $handover): void
    {
        if (in_array($this->journeyStage, ['live', 'support_only'], true) && $handover) {
            $daysLive = $handover->completed_at
                ? Carbon::parse($handover->completed_at)->diffInDays(now())
                : ($handover->go_live_date ? Carbon::parse($handover->go_live_date)->diffInDays(now()) : 0);

            $modules = collect([
                'TA' => ['flag' => $handover->ta, 'label' => 'Attendance'],
                'TL' => ['flag' => $handover->tl, 'label' => 'Leave'],
                'TC' => ['flag' => $handover->tc, 'label' => 'Claim'],
                'TP' => ['flag' => $handover->tp, 'label' => 'Payroll'],
            ])->filter(fn ($m) => $m['flag'])->values()->toArray();

            $this->heroCompanion = [
                'type' => 'live_status',
                'daysLive' => $daysLive,
                'modules' => $modules,
                'projectCode' => $handover->project_code ?? null,
            ];
            return;
        }

        if ($this->journeyStage === 'pre_kickoff') {
            $this->heroCompanion = [
                'type' => 'welcome',
                'steps' => [
                    ['icon' => 'fa-calendar-check', 'label' => 'Your kick-off session is being scheduled', 'done' => false],
                    ['icon' => 'fa-file-pdf', 'label' => 'Review the Software Handover document', 'done' => false],
                    ['icon' => 'fa-users', 'label' => 'Confirm attendees for your kick-off', 'done' => false],
                ],
            ];
            return;
        }

        $next = $leadId
            ? ImplementerAppointment::where('lead_id', $leadId)
                ->upcoming()
                ->orderBy('date')
                ->orderBy('start_time')
                ->first()
            : null;

        if ($next) {
            $date = Carbon::parse($next->date);
            $this->heroCompanion = [
                'type' => 'next_session',
                'day' => $date->format('d'),
                'month' => strtoupper($date->format('M')),
                'weekday' => $date->format('l'),
                'session' => $next->session ?: 'Implementation Session',
                'title' => $next->title ?: $next->session,
                'timeRange' => $next->formatted_time_range,
                'implementer' => $next->implementer ?: 'Your implementer',
                'meetingLink' => $next->meeting_link,
                'appointmentType' => $next->appointment_type,
            ];
            return;
        }

        $this->heroCompanion = [
            'type' => 'no_session',
            'message' => 'No upcoming sessions scheduled.',
        ];
    }

    private function loadActionItems($customer, ?int $leadId): void
    {
        $items = [];

        if ($leadId) {
            $pendingClient = ImplementerTicket::where('lead_id', $leadId)
                ->where('status', ImplementerTicketStatus::PENDING_CLIENT->value)
                ->orderBy('pending_client_since', 'desc')
                ->limit(5)
                ->get();

            foreach ($pendingClient as $ticket) {
                $age = $ticket->pending_client_since
                    ? Carbon::parse($ticket->pending_client_since)->diffForHumans(null, true)
                    : $ticket->created_at?->diffForHumans(null, true);

                $items[] = [
                    'type' => 'ticket',
                    'icon' => 'fa-comments',
                    'title' => 'Reply to ticket ' . $ticket->formatted_ticket_number,
                    'subtitle' => \Illuminate\Support\Str::limit($ticket->subject, 60),
                    'age' => $age,
                    'urgent' => $ticket->isOverdue(),
                    'url' => '?tab=impThread&ticket=' . $ticket->id,
                ];
            }

            $rejectedFiles = CustomerDataMigrationFile::where('lead_id', $leadId)
                ->where('status', 'Rejected')
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($rejectedFiles as $file) {
                $items[] = [
                    'type' => 'migration_rejected',
                    'icon' => 'fa-rotate-left',
                    'title' => 'Re-upload "' . $file->item . '" (' . $file->section . ')',
                    'subtitle' => $file->implementer_remark ? \Illuminate\Support\Str::limit($file->implementer_remark, 60) : 'Implementer requested changes',
                    'age' => $file->updated_at?->diffForHumans(null, true),
                    'urgent' => false,
                    'url' => '?tab=dataMigration',
                ];
            }
        }

        $this->actionItemsTotal = count($items);
        $this->actionItems = array_slice($items, 0, 5);
    }

    private function loadTickets(?int $leadId): void
    {
        if (! $leadId) {
            $this->tickets = [];
            $this->ticketsTotal = 0;
            $this->ticketsHealthState = 'on_track';
            return;
        }

        $openStatuses = [
            ImplementerTicketStatus::OPEN->value,
            ImplementerTicketStatus::PENDING_RND->value,
            ImplementerTicketStatus::PENDING_CLIENT->value,
        ];

        $this->ticketsTotal = ImplementerTicket::where('lead_id', $leadId)
            ->whereIn('status', $openStatuses)
            ->count();

        $tickets = ImplementerTicket::where('lead_id', $leadId)
            ->whereIn('status', $openStatuses)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        $worst = 'on_track';
        $rows = [];
        foreach ($tickets as $ticket) {
            $sla = $ticket->getSlaStatus();
            if ($sla === 'overdue') {
                $worst = 'overdue';
            } elseif ($sla === 'at_risk' && $worst !== 'overdue') {
                $worst = 'at_risk';
            }

            $rows[] = [
                'id' => $ticket->id,
                'number' => $ticket->formatted_ticket_number,
                'subject' => \Illuminate\Support\Str::limit($ticket->subject, 50),
                'sla' => $sla,
                'timeRemaining' => $ticket->getTimeRemaining(),
                'replyCount' => $ticket->replies()->count(),
                'statusLabel' => $ticket->status?->label() ?? 'Open',
                'url' => '?tab=impThread&ticket=' . $ticket->id,
            ];
        }
        $this->tickets = $rows;
        $this->ticketsHealthState = $this->ticketsTotal === 0 ? 'on_track' : $worst;
    }

    private function loadMigration(?int $leadId): void
    {
        if (! $leadId) {
            return;
        }

        $rows = CustomerDataMigrationFile::where('lead_id', $leadId)
            ->select('section', 'item', 'status')
            ->selectRaw('MAX(version) as latest_version')
            ->groupBy('section', 'item', 'status')
            ->get();

        $latestPerItem = [];
        foreach ($rows as $row) {
            $key = $row->section . '|' . $row->item;
            if (! isset($latestPerItem[$key]) || $row->latest_version > $latestPerItem[$key]['version']) {
                $latestPerItem[$key] = [
                    'status' => $row->status,
                    'version' => $row->latest_version,
                ];
            }
        }

        $counts = ['pending' => 0, 'submitted' => 0, 'approved' => 0, 'rejected' => 0];
        foreach ($latestPerItem as $entry) {
            $status = strtolower((string) $entry['status']);
            if (isset($counts[$status])) {
                $counts[$status]++;
            } elseif (in_array($status, ['', null], true)) {
                $counts['pending']++;
            }
        }

        $total = count($latestPerItem);
        $completed = $counts['approved'];
        $this->migrationCounts = [
            'pending' => $counts['pending'],
            'submitted' => $counts['submitted'],
            'approved' => $counts['approved'],
            'rejected' => $counts['rejected'],
            'total' => $total,
            'completed' => $completed,
            'percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
        ];
    }

    private function loadActivity($customer): void
    {
        if (! $customer) {
            return;
        }

        $this->unreadNotifications = $customer->unreadNotifications()->count();

        $items = $customer->notifications()->latest()->take(6)->get();
        $rows = [];
        foreach ($items as $n) {
            $data = $n->data ?? [];
            $message = $data['message'] ?? ($data['title'] ?? 'Notification');
            $entityId = $data['entity_id'] ?? null;
            $url = $entityId
                ? ('?tab=impThread&ticket=' . $entityId)
                : null;

            $rows[] = [
                'id' => $n->id,
                'icon' => $this->iconForNotificationType($n->type, $data),
                'message' => $message,
                'time' => optional($n->created_at)->diffForHumans(null, true) . ' ago',
                'url' => $url,
                'unread' => is_null($n->read_at),
            ];
        }
        $this->recentActivity = $rows;
    }

    private function iconForNotificationType(?string $type, array $data): string
    {
        $action = $data['action'] ?? '';
        return match ($action) {
            'replied_by_implementer' => 'fa-reply',
            'status_changed' => 'fa-shuffle',
            'closed' => 'fa-circle-check',
            'merged' => 'fa-code-merge',
            default => 'fa-bell',
        };
    }

    private function loadResources(?int $leadId): void
    {
        if (! $leadId) {
            $this->resources = [];
            return;
        }

        $rows = [];

        $latestHandoverFile = SoftwareHandoverProcessFile::where('lead_id', $leadId)
            ->orderBy('version', 'desc')
            ->first();

        if ($latestHandoverFile) {
            $rows[] = [
                'type' => 'handover_file',
                'icon' => 'fa-file-pdf',
                'title' => $latestHandoverFile->file_name,
                'meta' => 'Handover Doc · v' . $latestHandoverFile->version,
                'updated' => optional($latestHandoverFile->updated_at)->diffForHumans(null, true) . ' ago',
                'url' => route('customer.software-handover-process.download', ['file' => $latestHandoverFile->id]),
                'external' => false,
            ];
        }

        $recordings = ImplementerAppointment::where('lead_id', $leadId)
            ->whereNotNull('session_recording_link')
            ->where('session_recording_link', '!=', '')
            ->orderBy('date', 'desc')
            ->limit(3)
            ->get();

        foreach ($recordings as $rec) {
            $rows[] = [
                'type' => 'recording',
                'icon' => 'fa-video',
                'title' => $rec->session ?: 'Session Recording',
                'meta' => optional(Carbon::parse($rec->date))->format('d M Y'),
                'updated' => null,
                'url' => $rec->session_recording_link,
                'external' => true,
            ];
        }

        $this->resources = array_slice($rows, 0, 4);
    }

    private function loadQuickActions(): void
    {
        $base = [
            ['key' => 'create_ticket', 'icon' => 'fa-circle-plus', 'label' => 'New Support Ticket', 'url' => '?tab=impThread'],
            ['key' => 'book_session', 'icon' => 'fa-calendar-plus', 'label' => 'Book a Session', 'url' => '?tab=calendar'],
            ['key' => 'upload_migration', 'icon' => 'fa-upload', 'label' => 'Upload Migration File', 'url' => '?tab=dataMigration'],
        ];

        if ($this->hasProjectPlan) {
            $base[] = ['key' => 'view_project', 'icon' => 'fa-tasks', 'label' => 'View Project Plan', 'url' => '?tab=project'];
        } else {
            $base[] = ['key' => 'handover_doc', 'icon' => 'fa-file-export', 'label' => 'Handover Documents', 'url' => '?tab=softwareHandover'];
        }

        $base[] = ['key' => 'browse_webinars', 'icon' => 'fa-graduation-cap', 'label' => 'Webinars & Decks', 'url' => '?tab=webinar'];

        $this->quickActions = $base;
    }

    public function render()
    {
        return view('livewire.customer-dashboard');
    }
}

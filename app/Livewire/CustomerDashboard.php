<?php

namespace App\Livewire;

use App\Enums\ImplementerTicketStatus;
use App\Models\CustomerDataMigrationFile;
use App\Models\ImplementerAppointment;
use App\Models\ImplementerTicket;
use App\Models\SoftwareHandover;
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

    /** Sparkline of project-progress % at the last 8 weekly endpoints (oldest → newest). */
    public array $progressSpark = [
        'points' => [],
        'labels' => [],
    ];

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

    public array $quickActions = [];

    public int $daysLive = 0;

    public string $defaultMode = 'implementation';

    /** Project codes whose dashboards get demo-filled values for sales/UAT viewing. */
    private const DEMO_PROJECT_CODES = ['SW_260005'];

    /** Demo Go-Live date — pinned to the "19 Jun" stage date in applyDemoOverrides(). */
    private const DEMO_GO_LIVE_DATE = '2026-06-19';

    /** Implementation-mode stat strip values */
    public array $implStats = [
        'days_to_go_live' => null,   // int days remaining (negative = past)
        'sessions_held' => 0,
        'sessions_total' => 0,
        'modules_active' => 0,
    ];

    /** Implementer-thread (ImplementerTicket) summary — shown inside Implementation mode */
    public array $threadStats = [
        'open_count' => 0,
        'sla_health_pct' => null,    // null when no resolved tickets yet
        'avg_resolve_days' => null,  // null when no resolved tickets yet
        'tickets_30d' => 0,
    ];

    /** Support thread (separate support_threads table) — shown inside Support mode */
    public array $supportThreadStats = [
        'open_count' => 0,
        'total_count' => 0,
        'last_activity' => null,        // human-readable e.g. "3 days ago", or null
        'last_status' => null,          // last thread's status, or null
    ];
    public array $supportThreads = [];  // up to 5 recent rows

    /** Support thread counts grouped into 4 status buckets — drives the Support stat strip */
    public array $supportStatusCounts = [
        'open' => 0,
        'in_progress' => 0,
        'waiting_reply' => 0,
        'closed' => 0,
    ];

    public function mount(): void
    {
        $this->loadAll();
    }

    public function refresh(): void
    {
        $this->loadAll();
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
        $this->loadQuickActions();
        $this->daysLive = ($this->heroCompanion['type'] ?? null) === 'live_status'
            ? (int) ($this->heroCompanion['daysLive'] ?? 0)
            : 0;
        $this->loadImplStats($leadId, $handover);
        $this->loadProgressSparkline($leadId);
        $this->loadThreadStats($leadId);
        $this->loadSupportThreads($customer);
        // Default mode: 'support' only when the customer actually has support threads.
        // Otherwise 'implementation' (where the implementer-thread data lives).
        $this->defaultMode = $this->supportThreadStats['open_count'] > 0
            ? 'support'
            : 'implementation';

        $this->applyDemoOverrides($leadId);
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

        // First Review checkpoint: mid-progress, before final-review window opens.
        if ($overall >= 50) {
            return 'first_review';
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
            'first_review' => 'First review checkpoint — let\'s validate progress so far.',
            'pre_go_live' => 'Final review — go-live is around the corner.',
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
        ];
    }

    private function loadJourneyNodes(?SoftwareHandover $handover): void
    {
        $stages = [
            ['key' => 'kickoff', 'label' => 'Kick-Off', 'icon' => 'fa-clipboard-check'],
            ['key' => 'training', 'label' => 'Training', 'icon' => 'fa-clipboard-check'],
            ['key' => 'data_migration', 'label' => 'Data Migration', 'icon' => 'fa-clipboard-check'],
            ['key' => 'first_review', 'label' => 'First Review', 'icon' => 'fa-clipboard-check'],
            ['key' => 'final_review', 'label' => 'Final Review', 'icon' => 'fa-clipboard-check'],
            ['key' => 'go_live', 'label' => 'Go Live', 'icon' => 'fa-rocket'],
        ];

        $deepLinks = [
            'kickoff' => '?tab=calendar',
            'training' => '?tab=webinar',
            'data_migration' => '?tab=dataMigration',
            'first_review' => '?tab=project',
            'final_review' => '?tab=project',
            'go_live' => '?tab=impThread',
        ];

        $nodeIndexForCurrentStage = match ($this->journeyStage) {
            'pre_kickoff' => 0,                  // before kickoff — render as "in Kick-Off"
            'kickoff_done' => 1,                 // kickoff done, training is next
            'training' => 1,                     // explicitly in training window
            'data_migration' => 2,
            'first_review' => 3,
            'pre_go_live' => 4,                  // legacy state value → display "Final Review"
            'live', 'support_only' => 5,
            default => 0,
        };

        $datesByNode = [
            0 => optional($handover?->kick_off_meeting)->format('d M Y'),
            1 => optional($handover?->webinar_training)->format('d M Y'),
            2 => null,
            3 => null,
            4 => null,
            5 => optional($handover?->go_live_date)->format('d M Y'),
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
                'month' => $date->format('M'),
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
        $this->actionItems = array_slice($items, 0, 4);
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

    private function loadImplStats(?int $leadId, ?SoftwareHandover $handover): void
    {
        $daysToGoLive = null;
        if ($handover && $handover->go_live_date) {
            $daysToGoLive = (int) round(now()->startOfDay()->diffInDays(Carbon::parse($handover->go_live_date)->startOfDay(), false));
        }

        $sessionsHeld = 0;
        $sessionsTotal = 0;
        if ($leadId) {
            $sessionsTotal = ImplementerAppointment::where('lead_id', $leadId)->count();
            $sessionsHeld = ImplementerAppointment::where('lead_id', $leadId)
                ->where('status', 'Completed')
                ->count();
        }

        $modulesActive = 0;
        if ($handover) {
            foreach (['ta', 'tl', 'tc', 'tp'] as $flag) {
                if ($handover->{$flag}) {
                    $modulesActive++;
                }
            }
        }

        $this->implStats = [
            'days_to_go_live' => $daysToGoLive,
            'sessions_held' => $sessionsHeld,
            'sessions_total' => $sessionsTotal,
            'modules_active' => $modulesActive,
        ];
    }

    private function loadThreadStats(?int $leadId): void
    {
        // Implementer-thread summary (lives under Implementation mode).
        // Counts and averages from ImplementerTicket records.
        $this->threadStats = [
            'open_count' => $this->ticketsTotal,
            'sla_health_pct' => null,
            'avg_resolve_days' => null,
            'tickets_30d' => 0,
        ];

        if (! $leadId) {
            return;
        }

        $resolved = ImplementerTicket::where('lead_id', $leadId)
            ->whereNotNull('closed_at')
            ->get(['id', 'created_at', 'closed_at']);

        if ($resolved->count() > 0) {
            $withinSla = 0;
            $totalDays = 0.0;
            foreach ($resolved as $ticket) {
                if ($ticket->wasResolvedWithinSla()) {
                    $withinSla++;
                }
                $totalDays += Carbon::parse($ticket->created_at)
                    ->diffInHours(Carbon::parse($ticket->closed_at)) / 24.0;
            }
            $this->threadStats['sla_health_pct'] = (int) round(($withinSla / $resolved->count()) * 100);
            $this->threadStats['avg_resolve_days'] = round($totalDays / $resolved->count(), 1);
        }

        $this->threadStats['tickets_30d'] = ImplementerTicket::where('lead_id', $leadId)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
    }

    private function loadSupportThreads($customer): void
    {
        // Support Thread = separate post-go-live support system (`support_threads` table).
        // No Eloquent model exists yet, so query via DB facade.
        $this->supportThreadStats = [
            'open_count' => 0,
            'total_count' => 0,
            'last_activity' => null,
            'last_status' => null,
        ];
        $this->supportThreads = [];
        $this->supportStatusCounts = [
            'open' => 0,
            'in_progress' => 0,
            'waiting_reply' => 0,
            'closed' => 0,
        ];

        if (! $customer) {
            return;
        }

        if (! \Illuminate\Support\Facades\Schema::hasTable('support_threads')) {
            return;
        }

        $base = \Illuminate\Support\Facades\DB::table('support_threads')
            ->where('customer_id', $customer->id);

        $this->supportThreadStats['total_count'] = (clone $base)->count();
        $this->supportThreadStats['open_count'] = (clone $base)
            ->whereNotIn('status', ['closed', 'resolved'])
            ->count();

        $rows = (clone $base)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get(['id', 'thread_number', 'subject', 'status', 'module', 'updated_at']);

        $first = $rows->first();
        if ($first && $first->updated_at) {
            $this->supportThreadStats['last_activity'] = Carbon::parse($first->updated_at)->diffForHumans(null, true) . ' ago';
            $this->supportThreadStats['last_status'] = $first->status;
        }

        $this->supportThreads = $rows->map(fn ($r) => [
            'id' => $r->id,
            'number' => $r->thread_number ?: ('SUP-' . $r->id),
            'subject' => \Illuminate\Support\Str::limit($r->subject ?? 'Untitled thread', 60),
            'status' => $r->status ?: 'open',
            'module' => $r->module,
            'updated' => $r->updated_at ? Carbon::parse($r->updated_at)->diffForHumans(null, true) . ' ago' : null,
        ])->all();

        // Status bucket counts (Open / In Progress / Waiting Reply / Closed) for the stat strip
        $openLike       = ['open', 'new', 'unassigned'];
        $inProgressLike = ['in_progress', 'in-progress', 'inprogress', 'working', 'active'];
        $waitingLike    = ['pending', 'on_hold', 'on-hold', 'waiting', 'waiting_reply', 'awaiting', 'awaiting_customer', 'awaiting_reply'];
        $closedLike     = ['closed', 'resolved', 'done', 'fixed'];

        $buckets = ['open' => 0, 'in_progress' => 0, 'waiting_reply' => 0, 'closed' => 0];
        $statusGroups = (clone $base)
            ->selectRaw('LOWER(COALESCE(status, "")) as s, COUNT(*) as c')
            ->groupBy('s')
            ->get();

        foreach ($statusGroups as $row) {
            $s = trim((string) $row->s);
            $c = (int) $row->c;
            if (in_array($s, $closedLike, true)) {
                $buckets['closed'] += $c;
            } elseif (in_array($s, $waitingLike, true)) {
                $buckets['waiting_reply'] += $c;
            } elseif (in_array($s, $inProgressLike, true)) {
                $buckets['in_progress'] += $c;
            } else {
                // Unknown / empty status defaults to "open" so nothing falls off the radar
                $buckets['open'] += $c;
            }
        }

        $this->supportStatusCounts = $buckets;
    }

    private function loadQuickActions(): void
    {
        $base = [
            ['key' => 'create_ticket', 'icon' => 'fa-circle-plus', 'label' => 'New Project Thread', 'description' => 'Raise a new ticket for help or issues', 'url' => '?tab=impThread', 'category' => 'Support', 'color' => '#ef4444'],
            ['key' => 'book_session', 'icon' => 'fa-calendar-plus', 'label' => 'Book a Session', 'description' => 'Schedule a meeting with your implementer', 'url' => '?tab=calendar', 'category' => 'Calendar', 'color' => '#3b82f6'],
            ['key' => 'upload_migration', 'icon' => 'fa-upload', 'label' => 'Project File Template', 'description' => 'Download or upload migration data', 'url' => '?tab=dataMigration', 'category' => 'Data', 'color' => '#f59e0b'],
        ];

        if ($this->hasProjectPlan) {
            $base[] = ['key' => 'view_project', 'icon' => 'fa-tasks', 'label' => 'View Project Plan', 'description' => 'Track milestones and deliverables', 'url' => '?tab=project', 'category' => 'Project', 'color' => '#10b981'];
        } else {
            $base[] = ['key' => 'handover_doc', 'icon' => 'fa-file-export', 'label' => 'Handover Documents', 'description' => 'View signed handover files', 'url' => '?tab=softwareHandover', 'category' => 'Project', 'color' => '#10b981'];
        }

        $base[] = ['key' => 'browse_webinars', 'icon' => 'fa-graduation-cap', 'label' => 'Webinars & Decks', 'description' => 'Recordings, decks, and training', 'url' => '?tab=webinar', 'category' => 'Learning', 'color' => '#a855f7'];

        $this->quickActions = $base;
    }

    /**
     * Build an 8-point sparkline of project progress % at each weekly endpoint
     * over the last 8 weeks (oldest first → most recent last).
     *
     * Caveat: computed against current ProjectPlan rows, not historical snapshots.
     * Re-dating or re-assigning tasks shifts past points. A `project_progress_snapshots`
     * table would be the long-term fix; not in scope for this change.
     */
    private function loadProgressSparkline(?int $leadId): void
    {
        $points = array_fill(0, 8, 0);
        $labels = [];
        for ($w = 7; $w >= 0; $w--) {
            $endOfWeek = now()->subWeeks($w)->endOfWeek();
            $labels[] = $w === 0 ? 'Now' : $endOfWeek->format('M j');
        }

        if ($leadId) {
            $total = \App\Models\ProjectPlan::where('lead_id', $leadId)->count();
            if ($total > 0) {
                $windowStart = now()->subWeeks(8)->startOfWeek();

                $baseline = \App\Models\ProjectPlan::where('lead_id', $leadId)
                    ->where('status', 'completed')
                    ->whereNotNull('actual_end_date')
                    ->where('actual_end_date', '<', $windowStart)
                    ->count();

                $recent = \App\Models\ProjectPlan::where('lead_id', $leadId)
                    ->where('status', 'completed')
                    ->whereNotNull('actual_end_date')
                    ->where('actual_end_date', '>=', $windowStart)
                    ->orderBy('actual_end_date')
                    ->pluck('actual_end_date')
                    ->map(fn($d) => Carbon::parse($d));

                for ($w = 7; $w >= 0; $w--) {
                    $endOfWeek = now()->subWeeks($w)->endOfWeek();
                    $completed = $baseline + $recent->filter(fn($d) => $d->lessThanOrEqualTo($endOfWeek))->count();
                    $points[7 - $w] = (int) round(($completed / $total) * 100);
                }
            }
        }

        $this->progressSpark = ['points' => $points, 'labels' => $labels];
    }

    /**
     * Stage-aligned chart: one smooth Bezier curve through 6 journey stage points.
     * Returns ['line', 'area', 'dots' => [['x','y','isCurrent'], …], 'currentIdx'].
     * Coords use viewBox 600x200, full range (no internal padding) so HTML dot overlays
     * positioned with left/top percentages align exactly with the SVG path.
     */
    public function getSparkPathsProperty(): array
    {
        if (empty($this->journeyNodes)) {
            return ['line' => '', 'area' => '', 'dots' => [], 'currentIdx' => null];
        }

        $stageWeights = [5, 20, 35, 60, 80, 100];
        $actualPct    = (int) ($this->progressSummary['overallProgress'] ?? 0);
        $currentIdx   = null;

        $points = [];
        foreach ($this->journeyNodes as $i => $node) {
            if ($node['status'] === 'current') {
                $currentIdx = $i;
                $points[] = $actualPct ?: ($stageWeights[$i] ?? 50);
            } elseif ($node['status'] === 'done') {
                $points[] = $stageWeights[$i] ?? 100;
            } else {
                $points[] = $stageWeights[$i] ?? 50;
            }
        }

        $w = 600;
        $h = 200;
        $n = count($points);
        $coords = [];
        foreach ($points as $i => $val) {
            $x = $n === 1 ? $w / 2 : ($i / ($n - 1)) * $w;
            $y = $h - ($val / 100) * $h;
            $coords[] = [$x, $y];
        }

        // ── Soft "smooth wave" interpolation between milestones.
        //    Y follows a 50/50 blend of linear and sinusoidal easing:
        //        e(t) = 0.5·t + 0.5·½(1 − cos πt)
        //    Anchors stay exact (e(0)=0, e(1)=1) and the curve keeps
        //    a baseline slope at every milestone (e'(0)=e'(1)=0.5),
        //    so the visible plateau-shoulders of pure sinusoidal melt
        //    into gentle bends. Monotonic: e'(t) ≥ 0.5 on [0,1].
        $STEPS = 12;

        $wavyCoords = [];
        for ($i = 0; $i < $n - 1; $i++) {
            [$x1, $y1] = $coords[$i];
            [$x2, $y2] = $coords[$i + 1];
            $wavyCoords[] = [$x1, $y1];
            for ($s = 1; $s < $STEPS; $s++) {
                $t     = $s / $STEPS;
                $eased = 0.5 * $t + 0.25 * (1 - cos(M_PI * $t));
                $x     = $x1 + ($x2 - $x1) * $t;
                $y     = $y1 + ($y2 - $y1) * $eased;
                $wavyCoords[] = [$x, $y];
            }
        }
        $wavyCoords[] = $coords[$n - 1];
        $m = count($wavyCoords);

        // Catmull-Rom → cubic Bezier (tension = 6, canonical).
        // Endpoints duplicate the boundary so the curve starts/ends with zero tangent.
        $tension = 6;
        $line    = sprintf('M %.1f %.1f', $wavyCoords[0][0], $wavyCoords[0][1]);
        for ($i = 0; $i < $m - 1; $i++) {
            $p0 = $wavyCoords[max(0, $i - 1)];
            $p1 = $wavyCoords[$i];
            $p2 = $wavyCoords[$i + 1];
            $p3 = $wavyCoords[min($m - 1, $i + 2)];
            $cp1x = $p1[0] + ($p2[0] - $p0[0]) / $tension;
            $cp1y = $p1[1] + ($p2[1] - $p0[1]) / $tension;
            $cp2x = $p2[0] - ($p3[0] - $p1[0]) / $tension;
            $cp2y = $p2[1] - ($p3[1] - $p1[1]) / $tension;
            $line .= sprintf(' C %.1f %.1f, %.1f %.1f, %.1f %.1f',
                $cp1x, $cp1y, $cp2x, $cp2y, $p2[0], $p2[1]);
        }

        $first = $coords[0];
        $last  = end($coords);
        $area  = $line . sprintf(' L %.1f %.1f L %.1f %.1f Z', $last[0], $h, $first[0], $h);

        // Per-stage dots as percentages for HTML overlay positioning.
        $dots = [];
        foreach ($coords as $i => $c) {
            $yPct = ($c[1] / $h) * 100;
            $dots[] = [
                'x'         => ($c[0] / $w) * 100,
                'y'         => $yPct,
                'value'     => $points[$i],
                'isCurrent' => $i === $currentIdx,
                'tipBelow'  => $yPct < 30,
            ];
        }

        return [
            'line'       => $line,
            'area'       => $area,
            'dots'       => $dots,
            'currentIdx' => $currentIdx,
        ];
    }

    /**
     * Dynamic "on plan / behind / ahead" status pill for the Implementation Snapshot.
     * Compares actual project progress against the expected % for the current stage.
     */
    public function getSnapshotStatusProperty(): array
    {
        $actual = (int) ($this->progressSummary['overallProgress'] ?? 0);

        // Expected % at each journey stage (matches stageWeights in getSparkPathsProperty).
        $stageWeights = [5, 20, 35, 60, 80, 100];
        $currentIdx   = collect($this->journeyNodes)->search(fn($n) => ($n['status'] ?? '') === 'current');
        $expected     = is_int($currentIdx) ? ($stageWeights[$currentIdx] ?? 50) : 50;

        $delta = $actual - $expected;
        if ($delta >= 5)  return ['tone' => 'ahead',   'label' => $actual . '% ahead'];
        if ($delta <= -5) return ['tone' => 'behind',  'label' => $actual . '% behind'];
        return ['tone' => 'on-plan', 'label' => $actual . '% on plan'];
    }

    /**
     * If this customer is on a "demo" project (see DEMO_PROJECT_CODES), replace key
     * dashboard values with believable dummy data so the snapshot card and sparkline
     * are visible. Real customers are untouched.
     */
    private function applyDemoOverrides(?int $leadId): void
    {
        if (! $leadId) {
            return;
        }
        $handover = SoftwareHandover::where('lead_id', $leadId)->orderBy('id', 'desc')->first();
        $code = $handover->project_code ?? null;
        if (! $code || ! in_array($code, self::DEMO_PROJECT_CODES, true)) {
            return;
        }

        // --- Implementation Snapshot card ---
        // Prorated to match First Review stage (50–69% range; 60% sits mid-First-Review).
        $this->progressSpark['points'] = [8, 15, 22, 30, 38, 46, 53, 60];

        $this->progressSummary['overallProgress'] = 60;
        $this->progressSummary['completedTasks']  = 15;
        $this->progressSummary['totalTasks']      = 25;
        $this->hasProjectPlan                     = true;

        $this->migrationCounts['percent']   = 100;
        $this->migrationCounts['completed'] = 5;
        $this->migrationCounts['total']     = 5;
        $this->migrationCounts['approved']  = 5;

        // Days to Go-Live: match the demo "Go Live" stage date (19 Jun 2026) so the tile
        // agrees with the journey node. Sign is preserved — once the date passes, the
        // blade flips the label to "Days Live".
        $demoGoLive = Carbon::parse(self::DEMO_GO_LIVE_DATE)->startOfDay();
        $this->implStats['days_to_go_live'] = (int) round(
            now()->startOfDay()->diffInDays($demoGoLive, false)
        );
        // Implementer Threads tile shows "1" so the rose tile isn't empty.
        $this->ticketsTotal = 1;

        // --- Support panel telemetry ---
        $this->supportThreadStats = [
            'open_count'    => 2,
            'total_count'   => 15,
            'last_activity' => '3 days ago',
            'last_status'   => 'resolved',
        ];
        $this->supportStatusCounts = [
            'open'          => 2,
            'in_progress'   => 3,
            'waiting_reply' => 1,
            'closed'        => 9,
        ];
        // $this->supportThreads (table rows) intentionally left empty — demoing them would
        // create dead links to non-existent thread records.

        // --- Implementation Journey override ---
        $this->journeyStage = 'first_review';
        $this->stageCaption = $this->captionForStage($this->journeyStage);
        $this->loadJourneyNodes($handover);

        // Backfill demo dates on the middle stages (data_migration / first_review / final_review)
        // which have no DB column. Kick-Off and Go Live come through naturally via the handover
        // record but we still hardcode them here so the demo is consistent even if the DB
        // values drift over time.
        $demoStageDates = [
            0 => '17 Apr 2026',   // Kick-Off
            1 => '24 Apr 2026',   // Training
            2 => '1 May 2026',    // Data Migration
            3 => '8 May 2026',    // First Review
            4 => '29 May 2026',   // Final Review
            5 => '19 Jun 2026',   // Go Live
        ];
        foreach ($this->journeyNodes as $i => &$node) {
            if (isset($demoStageDates[$i])) {
                $node['date'] = $demoStageDates[$i];
            }
        }
        unset($node);
    }

    public function render()
    {
        return view('livewire.customer-dashboard');
    }
}

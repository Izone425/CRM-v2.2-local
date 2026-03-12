<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\TicketModule;
use App\Models\TicketProduct;
use App\Models\TicketPriority;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class TicketDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.ticket-dashboard';
    protected static ?string $navigationLabel = 'Ticket Dashboard';
    protected static ?string $title = '';

    public $selectedProduct = 'All Products';
    public $selectedModule = 'All Modules';
    public $selectedCategory = null;
    public $selectedStatus = null;
    public $selectedEnhancementStatus = null;
    public $selectedEnhancementType = null;
    public $currentMonth;
    public $currentYear;
    public $selectedDate;

    // Track individual combined statuses
    public $selectedCombinedStatuses = [];

    // New filter properties
    public $selectedPriority = null;
    public $selectedFrontEnd = null;
    public $selectedTicketStatus = null;
    public $etaStartDate = null;
    public $etaEndDate = null;
    public $etaSortDirection = null; // 'asc' or 'desc'

    // Filter modal property
    public $showFilterModal = false;

    // Loading state
    public $isLoading = false;

    // Pagination
    public $perPage = 15;
    public $currentPage = 1;

    // Search
    public $searchTicketId = '';

    // Column visibility
    public $showColumnToggle = false;
    public $visibleColumns = [
        'id' => true,
        'module' => false,
        'eta' => false,
        'status' => false,
        'frontend' => true,
        'completion_date' => true,
        'overdue' => true,
        'passfail' => true,
    ];

    protected $listeners = [
        'ticket-status-updated' => '$refresh',
    ];

    public function mount()
    {
        $this->currentMonth = now()->subHours(8)->month;
        $this->currentYear = now()->subHours(8)->year;

        // Set default filter to Completed status
        $this->selectedStatus = 'Completed';
    }

    /**
     * Dispatch event to open ticket modal via TicketModal component
     */
    public function viewTicket($ticketId): void
    {
        $this->dispatch('openTicketModal', $ticketId);
    }

    /**
     * Dispatch event to open reopen modal via TicketModal component
     */
    public function openReopenModal($ticketId): void
    {
        $this->dispatch('openTicketModal', $ticketId);
    }

    public function getViewData(): array
    {
        // Base query with optimized eager loading
        $baseQuery = Ticket::with(['product', 'module', 'priority', 'requestor'])
            ->whereIn('product_id', [1, 2]);

        // Apply product filter at database level
        if ($this->selectedProduct !== 'All Products') {
            $baseQuery->whereHas('product', function ($q) {
                $q->where('name', $this->selectedProduct);
            });
        }

        // Apply module filter at database level
        if ($this->selectedModule !== 'All Modules') {
            $baseQuery->whereHas('module', function ($q) {
                $q->where('name', $this->selectedModule);
            });
        }

        // Apply date filter at database level
        if ($this->selectedDate) {
            $baseQuery->whereDate('created_date', $this->selectedDate);
        }

        // Get all tickets for metrics (cached query)
        $tickets = $baseQuery->get();

        $softwareBugsMetrics = $this->calculateBugsMetrics($tickets);
        $backendAssistanceMetrics = $this->calculateBackendMetrics($tickets);
        $enhancementMetrics = $this->calculateEnhancementMetrics($tickets);
        $softwareBugsNewBreakdown = $this->getSoftwareBugsNewBreakdown($tickets);
        $softwareBugsInProgressBreakdown = $this->getSoftwareBugsInProgressBreakdown($tickets);
        $softwareBugsCompletedBreakdown = $this->getSoftwareBugsCompletedBreakdown($tickets);
        $softwareBugsClosedBreakdown = $this->getSoftwareBugsClosedBreakdown($tickets);
        $backendNewBreakdown = $this->getBackendNewBreakdown($tickets);
        $backendInProgressBreakdown = $this->getBackendInProgressBreakdown($tickets);
        $backendCompletedBreakdown = $this->getBackendCompletedBreakdown($tickets);
        $backendClosedBreakdown = $this->getBackendClosedBreakdown($tickets);

        $allFilteredTickets = $this->getFilteredTickets($tickets);

        // Pagination
        $totalTickets = $allFilteredTickets->count();
        $totalPages = ceil($totalTickets / $this->perPage);
        $this->currentPage = max(1, min($this->currentPage, $totalPages ?: 1));

        $filteredTickets = $allFilteredTickets
            ->slice(($this->currentPage - 1) * $this->perPage, $this->perPage)
            ->values();

        $calendarData = $this->getCalendarData();

        $products = TicketProduct::where('is_active', true)
            ->whereIn('id', [1, 2])
            ->pluck('name', 'name')
            ->toArray();

        // Filter modules to only show specific ones in defined sequence
        $allowedModules = ['Profile', 'Attendance', 'Leave', 'Claim', 'Payroll'];
        $allModules = TicketModule::where('is_active', true)
            ->whereIn('name', $allowedModules)
            ->pluck('name', 'name')
            ->toArray();

        // Sort modules by the defined sequence
        $modules = [];
        foreach ($allowedModules as $moduleName) {
            if (isset($allModules[$moduleName])) {
                $modules[$moduleName] = $allModules[$moduleName];
            }
        }

        // Get unique front end names
        $frontEndNames = $tickets->map(function ($ticket) {
            return $ticket->requestor->name ?? $ticket->requestor ?? null;
        })->filter()->unique()->sort()->values()->toArray();

        // Get unique statuses
        $statuses = $tickets->pluck('status')->unique()->sort()->values()->toArray();

        // Get unique priorities with proper sorting
        $priorities = TicketPriority::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('sort_order_suffix')
            ->get()
            ->map(function ($priority) {
                $label = 'P' . $priority->sort_order;
                if ($priority->sort_order_suffix) {
                    $label .= $priority->sort_order_suffix;
                }
                $label .= ' - ' . $priority->name;
                return [
                    'id' => $priority->id,
                    'name' => $priority->name,
                    'label' => $label,
                    'sort_order' => $priority->sort_order,
                    'sort_order_suffix' => $priority->sort_order_suffix,
                ];
            })
            ->toArray();

        return [
            'softwareBugs' => $softwareBugsMetrics,
            'backendAssistance' => $backendAssistanceMetrics,
            'enhancement' => $enhancementMetrics,
            'softwareBugsNewBreakdown' => $softwareBugsNewBreakdown,
            'softwareBugsInProgressBreakdown' => $softwareBugsInProgressBreakdown,
            'softwareBugsCompletedBreakdown' => $softwareBugsCompletedBreakdown,
            'softwareBugsClosedBreakdown' => $softwareBugsClosedBreakdown,
            'backendNewBreakdown' => $backendNewBreakdown,
            'backendInProgressBreakdown' => $backendInProgressBreakdown,
            'backendCompletedBreakdown' => $backendCompletedBreakdown,
            'backendClosedBreakdown' => $backendClosedBreakdown,
            'tickets' => $filteredTickets,
            'calendar' => $calendarData,
            'currentMonth' => $this->currentMonth,
            'currentYear' => $this->currentYear,
            'products' => $products,
            'modules' => $modules,
            'frontEndNames' => $frontEndNames,
            'statuses' => $statuses,
            'priorities' => $priorities,
            // Pagination data
            'totalTickets' => $totalTickets,
            'totalPages' => $totalPages,
            'currentPage' => $this->currentPage,
            'perPage' => $this->perPage,
        ];
    }

    public function markAsPassed(int $ticketId): void
    {
        try {
            $ticket = Ticket::find($ticketId);

            if ($ticket) {
                $authUser = auth()->user();

                $ticketSystemUser = null;
                if ($authUser) {
                    $ticketSystemUser = \Illuminate\Support\Facades\DB::connection('ticketingsystem_live')
                        ->table('users')
                        ->where('email', $authUser->email)
                        ->first();
                }

                $userId = $ticketSystemUser?->id ?? 22;
                $userName = $ticketSystemUser?->name ?? 'HRcrm User';
                $userRole = $ticketSystemUser?->role ?? 'Internal Staff';

                $oldStatus = $ticket->status;

                $ticket->update([
                    'status' => 'Closed',
                    'isPassed' => 1,
                    'passed_at' => now()->subHours(8),
                ]);

                // ✅ Create a log entry for marking ticket as passed
                TicketLog::create([
                    'ticket_id' => $ticket->id,
                    'old_value' => $oldStatus,
                    'new_value' => 'Closed',
                    'action' => "Marked ticket {$ticket->ticket_id} as passed - changed status from '{$oldStatus}' to 'Closed'.",
                    'field_name' => 'status',
                    'change_reason' => 'Ticket marked as passed',
                    'updated_by' => $userId,
                    'user_name' => $userName,
                    'user_role' => $userRole,
                    'change_type' => 'status_change',
                    'source' => 'dashboard_pass_action',
                    'created_at' => now()->subHours(8),
                    'updated_at' => now()->subHours(8),
                ]);

                // ✅ Show success notification
                Notification::make()
                    ->title('Ticket Marked as Passed')
                    ->body("Ticket {$ticket->ticket_id} has been marked as passed and closed")
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            Log::error('Error marking ticket as passed: ' . $e->getMessage());

            Notification::make()
                ->title('Error')
                ->body('Failed to mark ticket as passed')
                ->danger()
                ->send();
        }
    }

    public function markAsFailed(int $ticketId): void
    {
        try {
            $ticket = Ticket::find($ticketId);

            if ($ticket) {
                $authUser = auth()->user();

                $ticketSystemUser = null;
                if ($authUser) {
                    $ticketSystemUser = \Illuminate\Support\Facades\DB::connection('ticketingsystem_live')
                        ->table('users')
                        ->where('name', $authUser->name)
                        ->first();
                }

                $userId = $ticketSystemUser?->id ?? 22;
                $userName = $ticketSystemUser?->name ?? 'HRcrm User';
                $userRole = $ticketSystemUser?->role ?? 'Internal Staff';

                $oldStatus = $ticket->status;

                // ✅ Update ticket to Failed and Reopen status
                $ticket->update([
                    'isPassed' => 0,
                    'passed_at' => now()->subHours(8),
                    'status' => 'Reopen', // ✅ Change status to Reopen
                ]);

                // ✅ Create a log entry for status change
                TicketLog::create([
                    'ticket_id' => $ticket->id,
                    'old_value' => $oldStatus,
                    'new_value' => 'Reopen',
                    'updated_by' => $userId,
                    'user_name' => $userName,
                    'user_role' => $userRole,
                    'change_type' => 'status_change',
                    'source' => 'crm',
                    'remarks' => 'Ticket marked as failed and reopened',
                    'created_at' => now()->subHours(8),
                    'updated_at' => now()->subHours(8),
                ]);

                // ✅ Show success notification
                Notification::make()
                    ->title('Ticket Marked as Failed')
                    ->body("Ticket status changed to Reopen")
                    ->warning()
                    ->send();
            }
        } catch (\Exception $e) {
            Log::error('Error marking ticket as failed: ' . $e->getMessage());

            Notification::make()
                ->title('Error')
                ->body('Failed to update ticket status')
                ->danger()
                ->send();
        }
    }

    private function calculateBugsMetrics(Collection $tickets): array
    {
        $bugs = $tickets->filter(function ($ticket) {
            $priorityName = strtolower($ticket->priority?->name ?? '');

            return str_contains($priorityName, 'bug') ||
                str_contains($priorityName, 'software');
        });

        return [
            'total' => $bugs->count(),
            'new' => $bugs->where('status', 'New')->count(),
            'progress' => $bugs->whereIn('status', ['In Review', 'In Progress', 'Reopen'])->count(),
            'completed' => $bugs->whereIn('status', ['Completed', 'Tickets: Live'])->count(),
            'closed' => $bugs->whereIn('status', ['Closed', 'Closed System Configuration'])->count(),
        ];
    }

    private function getSoftwareBugsNewBreakdown(Collection $tickets): array
    {
        $bugs = $tickets->filter(function ($ticket) {
            $priorityName = strtolower($ticket->priority?->name ?? '');
            return (str_contains($priorityName, 'bug') || str_contains($priorityName, 'software'))
                && $ticket->status === 'New';
        });

        // Get requestor (frontend) names from users table
        $requestorNames = \Illuminate\Support\Facades\DB::connection('ticketingsystem_live')
            ->table('users')
            ->pluck('name', 'id');

        $breakdown = [];
        foreach ($bugs as $ticket) {
            $requestorName = $requestorNames[$ticket->requestor_id] ?? 'Unassigned';

            if (!isset($breakdown[$requestorName])) {
                $breakdown[$requestorName] = [
                    'count' => 0,
                    'tickets' => []
                ];
            }

            $breakdown[$requestorName]['count']++;
            $breakdown[$requestorName]['tickets'][] = $ticket->ticket_id;
        }

        // Sort by highest count
        uasort($breakdown, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        return $breakdown;
    }

    private function getSoftwareBugsInProgressBreakdown(Collection $tickets): array
    {
        $bugs = $tickets->filter(function ($ticket) {
            $priorityName = strtolower($ticket->priority?->name ?? '');
            return (str_contains($priorityName, 'bug') || str_contains($priorityName, 'software'))
                && in_array($ticket->status, ['In Review', 'In Progress', 'Reopen']);
        });

        // Get requestor (frontend) names from users table
        $requestorNames = \Illuminate\Support\Facades\DB::connection('ticketingsystem_live')
            ->table('users')
            ->pluck('name', 'id');

        $breakdown = [];
        foreach ($bugs as $ticket) {
            $requestorName = $requestorNames[$ticket->requestor_id] ?? 'Unassigned';

            if (!isset($breakdown[$requestorName])) {
                $breakdown[$requestorName] = [
                    'count' => 0,
                    'tickets' => []
                ];
            }

            $breakdown[$requestorName]['count']++;
            $breakdown[$requestorName]['tickets'][] = $ticket->ticket_id;
        }

        // Sort by highest count
        uasort($breakdown, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        return $breakdown;
    }

    private function getSoftwareBugsCompletedBreakdown(Collection $tickets): array
    {
        $bugs = $tickets->filter(function ($ticket) {
            $priorityName = strtolower($ticket->priority?->name ?? '');
            return (str_contains($priorityName, 'bug') || str_contains($priorityName, 'software'))
                && in_array($ticket->status, ['Completed', 'Tickets: Live']);
        });

        // Get requestor (frontend) names from users table
        $requestorNames = \Illuminate\Support\Facades\DB::connection('ticketingsystem_live')
            ->table('users')
            ->pluck('name', 'id');

        $breakdown = [];
        foreach ($bugs as $ticket) {
            $requestorName = $requestorNames[$ticket->requestor_id] ?? 'Unassigned';

            if (!isset($breakdown[$requestorName])) {
                $breakdown[$requestorName] = [
                    'count' => 0,
                    'tickets' => []
                ];
            }

            $breakdown[$requestorName]['count']++;
            $breakdown[$requestorName]['tickets'][] = $ticket->ticket_id;
        }

        // Sort by highest count
        uasort($breakdown, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        return $breakdown;
    }

    private function getSoftwareBugsClosedBreakdown(Collection $tickets): array
    {
        $bugs = $tickets->filter(function ($ticket) {
            $priorityName = strtolower($ticket->priority?->name ?? '');
            return (str_contains($priorityName, 'bug') || str_contains($priorityName, 'software'))
                && in_array($ticket->status, ['Closed', 'Closed System Configuration']);
        });

        // Get requestor (frontend) names from users table
        $requestorNames = \Illuminate\Support\Facades\DB::connection('ticketingsystem_live')
            ->table('users')
            ->pluck('name', 'id');

        $breakdown = [];
        foreach ($bugs as $ticket) {
            $requestorName = $requestorNames[$ticket->requestor_id] ?? 'Unassigned';

            if (!isset($breakdown[$requestorName])) {
                $breakdown[$requestorName] = [
                    'count' => 0,
                    'tickets' => []
                ];
            }

            $breakdown[$requestorName]['count']++;
            $breakdown[$requestorName]['tickets'][] = $ticket->ticket_id;
        }

        // Sort by highest count
        uasort($breakdown, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        return $breakdown;
    }

    private function getBackendNewBreakdown(Collection $tickets): array
    {
        $backend = $tickets->filter(function ($ticket) {
            $priorityName = strtolower($ticket->priority?->name ?? '');
            return (str_contains($priorityName, 'backend') || str_contains($priorityName, 'assistance') || str_contains(str_replace(' ', '', $priorityName), 'backend'))
                && $ticket->status === 'New';
        });

        // Get requestor (frontend) names from users table
        $requestorNames = \Illuminate\Support\Facades\DB::connection('ticketingsystem_live')
            ->table('users')
            ->pluck('name', 'id');

        $breakdown = [];
        foreach ($backend as $ticket) {
            $requestorName = $requestorNames[$ticket->requestor_id] ?? 'Unassigned';

            if (!isset($breakdown[$requestorName])) {
                $breakdown[$requestorName] = [
                    'count' => 0,
                    'tickets' => []
                ];
            }

            $breakdown[$requestorName]['count']++;
            $breakdown[$requestorName]['tickets'][] = $ticket->ticket_id;
        }

        // Sort by highest count
        uasort($breakdown, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        return $breakdown;
    }

    private function getBackendInProgressBreakdown(Collection $tickets): array
    {
        $backend = $tickets->filter(function ($ticket) {
            $priorityName = strtolower($ticket->priority?->name ?? '');
            return (str_contains($priorityName, 'backend') || str_contains($priorityName, 'assistance') || str_contains(str_replace(' ', '', $priorityName), 'backend'))
                && in_array($ticket->status, ['In Review', 'In Progress', 'Reopen']);
        });

        // Get requestor (frontend) names from users table
        $requestorNames = \Illuminate\Support\Facades\DB::connection('ticketingsystem_live')
            ->table('users')
            ->pluck('name', 'id');

        $breakdown = [];
        foreach ($backend as $ticket) {
            $requestorName = $requestorNames[$ticket->requestor_id] ?? 'Unassigned';

            if (!isset($breakdown[$requestorName])) {
                $breakdown[$requestorName] = [
                    'count' => 0,
                    'tickets' => []
                ];
            }

            $breakdown[$requestorName]['count']++;
            $breakdown[$requestorName]['tickets'][] = $ticket->ticket_id;
        }

        // Sort by highest count
        uasort($breakdown, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        return $breakdown;
    }

    private function getBackendCompletedBreakdown(Collection $tickets): array
    {
        $backend = $tickets->filter(function ($ticket) {
            $priorityName = strtolower($ticket->priority?->name ?? '');
            return (str_contains($priorityName, 'backend') || str_contains($priorityName, 'assistance') || str_contains(str_replace(' ', '', $priorityName), 'backend'))
                && in_array($ticket->status, ['Completed', 'Tickets: Live']);
        });

        // Get requestor (frontend) names from users table
        $requestorNames = \Illuminate\Support\Facades\DB::connection('ticketingsystem_live')
            ->table('users')
            ->pluck('name', 'id');

        $breakdown = [];
        foreach ($backend as $ticket) {
            $requestorName = $requestorNames[$ticket->requestor_id] ?? 'Unassigned';

            if (!isset($breakdown[$requestorName])) {
                $breakdown[$requestorName] = [
                    'count' => 0,
                    'tickets' => []
                ];
            }

            $breakdown[$requestorName]['count']++;
            $breakdown[$requestorName]['tickets'][] = $ticket->ticket_id;
        }

        // Sort by highest count
        uasort($breakdown, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        return $breakdown;
    }

    private function getBackendClosedBreakdown(Collection $tickets): array
    {
        $backend = $tickets->filter(function ($ticket) {
            $priorityName = strtolower($ticket->priority?->name ?? '');
            return (str_contains($priorityName, 'backend') || str_contains($priorityName, 'assistance') || str_contains(str_replace(' ', '', $priorityName), 'backend'))
                && in_array($ticket->status, ['Closed', 'Closed System Configuration']);
        });

        // Get requestor (frontend) names from users table
        $requestorNames = \Illuminate\Support\Facades\DB::connection('ticketingsystem_live')
            ->table('users')
            ->pluck('name', 'id');

        $breakdown = [];
        foreach ($backend as $ticket) {
            $requestorName = $requestorNames[$ticket->requestor_id] ?? 'Unassigned';

            if (!isset($breakdown[$requestorName])) {
                $breakdown[$requestorName] = [
                    'count' => 0,
                    'tickets' => []
                ];
            }

            $breakdown[$requestorName]['count']++;
            $breakdown[$requestorName]['tickets'][] = $ticket->ticket_id;
        }

        // Sort by highest count
        uasort($breakdown, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        return $breakdown;
    }

    private function calculateBackendMetrics(Collection $tickets): array
    {
        $backend = $tickets->filter(function ($ticket) {
            $priorityName = strtolower($ticket->priority?->name ?? '');

            return str_contains($priorityName, 'backend') ||
                str_contains($priorityName, 'assistance') ||
                str_contains(str_replace(' ', '', $priorityName), 'backend');
        });

        return [
            'total' => $backend->count(),
            'new' => $backend->where('status', 'New')->count(),
            'progress' => $backend->whereIn('status', ['In Review', 'In Progress', 'Reopen'])->count(),
            'completed' => $backend->whereIn('status', ['Completed', 'Tickets: Live'])->count(),
            'closed' => $backend->whereIn('status', ['Closed', 'Closed System Configuration'])->count(),
        ];
    }

    private function calculateEnhancementMetrics(Collection $tickets): array
    {
        $enhancements = $tickets->filter(function ($ticket) {
            $priorityName = strtolower($ticket->priority?->name ?? '');

            return str_contains($priorityName, 'enhancement') ||
                   str_contains($priorityName, 'critical enhancement') ||
                   str_contains($priorityName, 'paid') ||
                   str_contains($priorityName, 'customization') ||
                   str_contains($priorityName, 'non-critical');
        });

        if ($this->selectedEnhancementType) {
            $enhancements = $enhancements->filter(function ($ticket) {
                $priorityName = strtolower($ticket->priority?->name ?? '');

                switch ($this->selectedEnhancementType) {
                    case 'critical':
                        return str_contains($priorityName, 'critical enhancement');
                    case 'paid':
                        return str_contains($priorityName, 'paid customization');
                    case 'non-critical':
                        return str_contains($priorityName, 'non-critical enhancement');
                    default:
                        return true;
                }
            });
        }

        return [
            'total' => $enhancements->count(),
            'new' => $enhancements->where('status', 'New')->count(),
            'pending_release' => $enhancements->where('status', 'Pending Release')->count(),
            'system_go_live' => $enhancements->where('status', 'System Go Live')->count(),
        ];
    }

    private function getCalendarData(): array
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1);

        return [
            'month' => $date->format('F Y'),
            'days_in_month' => $date->daysInMonth,
            'first_day_of_week' => $date->dayOfWeek,
            'current_date' => Carbon::now()->addHours(8),
        ];
    }

    private function getFilteredTickets(Collection $tickets): Collection
    {
        return $tickets
            ->when($this->searchTicketId, function ($collection) {
                return $collection->filter(function ($ticket) {
                    return str_contains((string) $ticket->ticket_id, $this->searchTicketId);
                });
            })
            ->when($this->selectedPriority, function ($collection) {
                return $collection->filter(function ($ticket) {
                    $priorityName = $ticket->priority->name ?? '';
                    return $priorityName === $this->selectedPriority;
                });
            })
            ->when($this->selectedCategory, function ($collection) {
                return $collection->filter(function ($ticket) {
                    $priorityName = strtolower($ticket->priority?->name ?? '');

                    if ($this->selectedCategory === 'softwareBugs') {
                        return str_contains($priorityName, 'bug') ||
                               str_contains($priorityName, 'software');
                    }
                    elseif ($this->selectedCategory === 'backendAssistance') {
                        return str_contains($priorityName, 'backend') ||
                               str_contains($priorityName, 'assistance') ||
                               str_contains(str_replace(' ', '', $priorityName), 'backend');
                    }
                    elseif ($this->selectedCategory === 'enhancement') {
                        $isEnhancement = str_contains($priorityName, 'enhancement') ||
                                       str_contains($priorityName, 'paid') ||
                                       str_contains($priorityName, 'customization') ||
                                       str_contains($priorityName, 'non-critical');

                        if ($isEnhancement && $this->selectedEnhancementType) {
                            switch ($this->selectedEnhancementType) {
                                case 'critical':
                                    return $priorityName == 'critical enhancement';
                                case 'paid':
                                    return $priorityName == 'paid customization';
                                case 'non-critical':
                                    return $priorityName == 'non-critical enhancement';
                            }
                        }

                        return $isEnhancement;
                    }
                    return true;
                });
            })
            ->when($this->selectedStatus, function ($collection) {
                // Handle combined status with individual selections
                if (!empty($this->selectedCombinedStatuses)) {
                    return $collection->whereIn('status', $this->selectedCombinedStatuses);
                }
                // Handle combined In Progress status
                elseif ($this->selectedStatus === 'In Progress') {
                    return $collection->whereIn('status', ['In Review', 'In Progress', 'Reopen']);
                }
                // Handle combined Completed status
                elseif ($this->selectedStatus === 'Completed') {
                    return $collection->whereIn('status', ['Completed', 'Tickets: Live']);
                }
                // Handle combined Closed status
                elseif ($this->selectedStatus === 'Closed') {
                    return $collection->whereIn('status', ['Closed', 'Closed System Configuration']);
                }
                return $collection->where('status', $this->selectedStatus);
            })
            ->when($this->selectedEnhancementStatus, function ($collection) {
                return $collection->where('status', $this->selectedEnhancementStatus);
            })
            ->when($this->selectedFrontEnd, function ($collection) {
                return $collection->filter(function ($ticket) {
                    $frontEndName = $ticket->requestor->name ?? $ticket->requestor ?? '';
                    return $frontEndName === $this->selectedFrontEnd;
                });
            })
            ->when($this->selectedTicketStatus, function ($collection) {
                return $collection->where('status', $this->selectedTicketStatus);
            })
            ->when($this->etaStartDate, function ($collection) {
                return $collection->filter(function ($ticket) {
                    return $ticket->eta_release && $ticket->eta_release >= \Carbon\Carbon::parse($this->etaStartDate);
                });
            })
            ->when($this->etaEndDate, function ($collection) {
                return $collection->filter(function ($ticket) {
                    return $ticket->eta_release && $ticket->eta_release <= \Carbon\Carbon::parse($this->etaEndDate);
                });
            })
            ->when($this->etaSortDirection, function ($collection) {
                if ($this->etaSortDirection === 'asc') {
                    return $collection->sortBy(function ($ticket) {
                        return $ticket->eta_release ?? \Carbon\Carbon::maxValue();
                    });
                } else {
                    return $collection->sortByDesc(function ($ticket) {
                        return $ticket->eta_release ?? \Carbon\Carbon::minValue();
                    });
                }
            }, function ($collection) {
                // Sort by latest completion date and time
                return $collection->sortByDesc(function ($ticket) {
                    $completionLog = \Illuminate\Support\Facades\DB::connection('ticketingsystem_live')
                        ->table('ticket_logs')
                        ->where('ticket_id', $ticket->id)
                        ->whereIn('new_value', ['Completed', 'Live'])
                        ->orderBy('created_at', 'desc')
                        ->first();

                    return $completionLog ? \Carbon\Carbon::parse($completionLog->created_at) : \Carbon\Carbon::minValue();
                });
            })
            ->values();
    }

    public function selectCategory($category, $status = null): void
    {
        if ($this->selectedCategory === $category && $this->selectedStatus === $status) {
            $this->selectedCategory = null;
            $this->selectedStatus = null;
            $this->selectedCombinedStatuses = [];
        } else {
            $this->selectedCategory = $category;
            $this->selectedStatus = $status;
            $this->selectedEnhancementStatus = null;

            // Handle combined statuses
            if ($status === 'In Progress') {
                $this->selectedCombinedStatuses = ['In Review', 'In Progress', 'Reopen'];
            } elseif ($status === 'Closed') {
                $this->selectedCombinedStatuses = ['Closed', 'Closed System Configuration'];
            } else {
                $this->selectedCombinedStatuses = [];
            }
        }
        $this->currentPage = 1; // Reset pagination when filter changes
    }

    public function selectEnhancementType($type): void
    {
        if ($this->selectedEnhancementType === $type) {
            $this->selectedEnhancementType = null;
        } else {
            $this->selectedEnhancementType = $type;
            $this->selectedCategory = 'enhancement';
        }
        $this->currentPage = 1; // Reset pagination when filter changes
    }

    public function removeIndividualStatus($statusToRemove): void
    {
        if (!empty($this->selectedCombinedStatuses)) {
            $this->selectedCombinedStatuses = array_diff($this->selectedCombinedStatuses, [$statusToRemove]);

            // If no statuses left, clear the filter
            if (empty($this->selectedCombinedStatuses)) {
                $this->selectedCategory = null;
                $this->selectedStatus = null;
            }
        }
    }

    public function selectDate($year, $month, $day): void
    {
        $selectedDate = Carbon::create($year, $month, $day)->format('Y-m-d');

        if ($this->selectedDate === $selectedDate) {
            $this->selectedDate = null;
        } else {
            $this->selectedDate = $selectedDate;
        }
        $this->currentPage = 1; // Reset pagination when filter changes
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function toggleEtaSort(): void
    {
        if ($this->etaSortDirection === null) {
            $this->etaSortDirection = 'asc';
        } elseif ($this->etaSortDirection === 'asc') {
            $this->etaSortDirection = 'desc';
        } else {
            $this->etaSortDirection = null;
        }
    }

    public function openFilterModal(): void
    {
        $this->showFilterModal = true;
    }

    public function closeFilterModal(): void
    {
        $this->showFilterModal = false;
    }

    public function toggleColumnVisibility(): void
    {
        $this->showColumnToggle = !$this->showColumnToggle;
    }

    public function closeColumnToggle(): void
    {
        $this->showColumnToggle = false;
    }

    public function toggleColumn(string $column): void
    {
        if (isset($this->visibleColumns[$column])) {
            $this->visibleColumns[$column] = !$this->visibleColumns[$column];
        }
    }

    public function clearAllFilters(): void
    {
        // Batch update to prevent multiple renders
        $this->reset([
            'selectedPriority',
            'selectedFrontEnd',
            'selectedTicketStatus',
            'etaStartDate',
            'etaEndDate',
            'etaSortDirection',
            'searchTicketId'
        ]);

        $this->selectedProduct = 'All Products';
        $this->selectedModule = 'All Modules';
        $this->currentPage = 1; // Reset to first page when filters change
    }

    public function updatedSearchTicketId(): void
    {
        $this->currentPage = 1; // Reset to first page when search changes
    }

    public function goToPage(int $page): void
    {
        $this->currentPage = $page;
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function nextPage(): void
    {
        $this->currentPage++;
    }

    public function resetPagination(): void
    {
        $this->currentPage = 1;
    }
}

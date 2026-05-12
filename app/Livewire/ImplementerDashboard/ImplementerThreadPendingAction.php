<?php

namespace App\Livewire\ImplementerDashboard;

use App\Enums\ImplementerTicketStatus;
use App\Models\ImplementerTicket;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Component;

// Note: implementer roles are 4 and 5 (kept in sync with ImplementerRequestPendingApproval).
class ImplementerThreadPendingAction extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $selectedUser;
    public string $statusFilter = 'all'; // 'all'|'overdue'|'today'|'upcoming'
    public $lastRefreshTime;

    public function mount(): void
    {
        $this->lastRefreshTime = now()->format('Y-m-d H:i:s');
    }

    public function refreshTable(): void
    {
        $this->resetTable();
        $this->lastRefreshTime = now()->format('Y-m-d H:i:s');

        Notification::make()
            ->title('Table refreshed')
            ->success()
            ->send();
    }

    #[On('refresh-implementer-tables')]
    public function refreshData(): void
    {
        $this->resetTable();
        $this->lastRefreshTime = now()->format('Y-m-d H:i:s');
    }

    #[On('updateTablesForUser')]
    public function updateTablesForUser($selectedUser): void
    {
        if ($selectedUser) {
            $this->selectedUser = $selectedUser;
            session(['selectedUser' => $selectedUser]);
        } else {
            $this->selectedUser = 7;
            session(['selectedUser' => 7]);
        }

        $this->resetTable();
    }

    #[On('thread-filter-changed')]
    public function setStatusFilter($value): void
    {
        $this->statusFilter = in_array($value, ['all', 'overdue', 'today', 'upcoming'], true) ? $value : 'all';
        $this->resetTable();
    }

    public function getPendingThreads(): Builder
    {
        $this->selectedUser = $this->selectedUser ?? session('selectedUser') ?? auth()->user()->id;

        $query = ImplementerTicket::query()
            ->whereNull('first_responded_at')
            ->where('status', '!=', ImplementerTicketStatus::CLOSED->value)
            ->with(['lead.companyDetail', 'customer', 'implementerUser']);

        if ($this->selectedUser === 'all-implementer') {
            // no filter
        } elseif (is_numeric($this->selectedUser)) {
            $user = User::find($this->selectedUser);
            if ($user && in_array($user->role_id, [4, 5], true)) {
                $query->where('implementer_user_id', $user->id);
            } else {
                $query->whereRaw('1=0');
            }
        } else {
            $me = auth()->user();
            if ($me && in_array($me->role_id, [4, 5], true)) {
                $query->where('implementer_user_id', $me->id);
            } else {
                $query->whereRaw('1=0');
            }
        }

        return $query;
    }

    protected function bucketedTicketIds(string $bucket): array
    {
        $tz = config('app.timezone');
        $now = now($tz);
        $today = $now->copy()->startOfDay();
        $tomorrow = $today->copy()->addDay();

        return $this->getPendingThreads()
            ->get(['id', 'created_at', 'is_overdue'])
            ->filter(function (ImplementerTicket $t) use ($bucket, $now, $today, $tomorrow, $tz) {
                $dl = $t->getFirstReplyDeadline()?->copy()->setTimezone($tz);
                // A ticket is overdue if flagged OR if its deadline has elapsed (flag may lag the scheduled command).
                $isOverdue = $t->is_overdue || ($dl && $now->gt($dl));

                if ($bucket === 'overdue') {
                    return $isOverdue;
                }
                if ($isOverdue || !$dl) {
                    return false;
                }
                if ($bucket === 'today') {
                    return $dl->gte($today) && $dl->lt($tomorrow);
                }
                if ($bucket === 'upcoming') {
                    return $dl->gte($tomorrow);
                }
                return true;
            })
            ->pluck('id')
            ->all();
    }

    public function getAllCount(): int
    {
        return $this->getPendingThreads()->count();
    }

    public function getOverdueCount(): int
    {
        return count($this->bucketedTicketIds('overdue'));
    }

    public function getDueTodayCount(): int
    {
        return count($this->bucketedTicketIds('today'));
    }

    public function getUpcomingCount(): int
    {
        return count($this->bucketedTicketIds('upcoming'));
    }

    public function table(Table $table): Table
    {
        $baseQuery = $this->getPendingThreads();

        if ($this->statusFilter !== 'all') {
            $ids = $this->bucketedTicketIds($this->statusFilter);
            $baseQuery->whereIn('id', $ids ?: [0]);
        }

        return $table
            ->poll('300s')
            ->query($baseQuery)
            ->defaultSort('created_at', 'asc')
            ->emptyState(fn () => view('components.empty-state-question'))
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 25])
            ->recordUrl(fn (ImplementerTicket $record) =>
                route('filament.admin.pages.implementer-ticketing-dashboard')
                . '?ticket=' . $record->id
                . '&from=' . urlencode(request()->fullUrl())
            )
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('Ticket #')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject')
                    ->label('Subject')
                    ->limit(60)
                    ->tooltip(fn (ImplementerTicket $record): ?string => $record->subject)
                    ->searchable(),

                TextColumn::make('lead.companyDetail.company_name')
                    ->label('Customer / Company')
                    ->getStateUsing(fn (ImplementerTicket $record): string =>
                        $record->lead?->companyDetail?->company_name ?? '—'
                    )
                    ->description(fn (ImplementerTicket $record): ?string => $record->customer?->name)
                    ->searchable(['lead.companyDetail.company_name', 'customer.name']),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->tooltip(fn (ImplementerTicket $record): string =>
                        $record->created_at->setTimezone(config('app.timezone'))->format('d M Y H:i')
                    )
                    ->sortable(),

                TextColumn::make('sla_badge')
                    ->label('SLA')
                    ->badge()
                    ->getStateUsing(function (ImplementerTicket $record): string {
                        $tz = config('app.timezone');
                        $now = now($tz);
                        $dl = $record->getFirstReplyDeadline()?->copy()->setTimezone($tz);
                        if ($record->is_overdue || ($dl && $now->gt($dl))) {
                            return 'overdue';
                        }
                        if (!$dl) {
                            return 'upcoming';
                        }
                        $today = $now->copy()->startOfDay();
                        $tomorrow = $today->copy()->addDay();
                        if ($dl->gte($today) && $dl->lt($tomorrow)) {
                            return 'today';
                        }
                        return 'upcoming';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'overdue' => 'danger',
                        'today' => 'warning',
                        'upcoming' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(function (string $state, ImplementerTicket $record): string {
                        $tz = config('app.timezone');
                        $now = now($tz);
                        $dl = $record->getFirstReplyDeadline()?->copy()->setTimezone($tz);

                        if ($state === 'overdue') {
                            if ($dl) {
                                return 'Overdue by ' . $now->diff($dl)->format('%dd %hh') ;
                            }
                            return 'Overdue';
                        }
                        if ($state === 'today' && $dl) {
                            $minutes = (int) max(0, $now->diffInMinutes($dl, false));
                            if ($minutes < 60) {
                                return 'Due in ' . $minutes . 'm';
                            }
                            return 'Due in ' . floor($minutes / 60) . 'h ' . ($minutes % 60) . 'm';
                        }
                        if ($state === 'upcoming' && $dl) {
                            if ($dl->isTomorrow()) {
                                return 'Due tomorrow';
                            }
                            return 'Due ' . $dl->format('D, d M');
                        }
                        return ucfirst($state);
                    }),

                TextColumn::make('priority')
                    ->label('Priority')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'low' => 'gray',
                        'normal' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : '—'),
            ]);
    }

    public function render()
    {
        return view('livewire.implementer_dashboard.implementer-thread-pending-action');
    }
}

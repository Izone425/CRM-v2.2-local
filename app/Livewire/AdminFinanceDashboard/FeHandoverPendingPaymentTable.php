<?php

namespace App\Livewire\AdminFinanceDashboard;

use Livewire\Component;
use App\Models\ResellerHandoverFe;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class FeHandoverPendingPaymentTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $lastRefreshTime;
    public $showFilesModal = false;
    public $selectedHandover = null;
    public $handoverFiles = [];
    public $showRemarkModal = false;
    public $showAdminRemarkModal = false;

    public function mount()
    {
        $this->lastRefreshTime = now()->format('Y-m-d H:i:s');
    }

    public function refreshTable()
    {
        $this->resetTable();
        $this->lastRefreshTime = now()->format('Y-m-d H:i:s');

        Notification::make()
            ->title('Table refreshed')
            ->success()
            ->send();
    }

    #[On('refresh-leadowner-tables')]
    public function refreshData()
    {
        $this->resetTable();
        $this->lastRefreshTime = now()->format('Y-m-d H:i:s');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ResellerHandoverFe::query()->where('status', 'pending_reseller_payment')->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('fe_id')
                    ->label('FE ID')
                    ->sortable()
                    ->action(
                        Action::make('view_files')
                            ->label('View Files')
                            ->action(fn (ResellerHandoverFe $record) => $this->openFilesModal($record->id))
                    )
                    ->color('primary')
                    ->weight('bold')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereRaw("CONCAT('FE', LPAD(MONTH(created_at), 2, '0'), '-', LPAD(id, 4, '0')) LIKE ?", ["%{$search}%"]);
                    }),
                TextColumn::make('autocount_invoice_number')
                    ->label('A/C Invoice')
                    ->searchable(),
                TextColumn::make('ap_document')
                    ->label('AP Document')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('reseller_company_name')
                    ->label('Reseller Name')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subscriber_name')
                    ->label('Subscriber Name')
                    ->wrap()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending_reseller_payment',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('overdue')
                    ->label('Overdue')
                    ->getStateUsing(function (ResellerHandoverFe $record) {
                        $today = now()->startOfDay();
                        $updatedAt = $record->updated_at->startOfDay();
                        $daysDiff = $today->diffInDays($updatedAt);

                        return $daysDiff == 0 ? '0 Day' : '-' . $daysDiff . ' Days';
                    })
                    ->color(function (ResellerHandoverFe $record) {
                        $today = now()->startOfDay();
                        $updatedAt = $record->updated_at->startOfDay();
                        $daysDiff = $today->diffInDays($updatedAt);

                        return $daysDiff == 0 ? 'success' : 'danger';
                    })
                    ->weight(function (ResellerHandoverFe $record) {
                        $today = now()->startOfDay();
                        $updatedAt = $record->updated_at->startOfDay();
                        $daysDiff = $today->diffInDays($updatedAt);

                        return $daysDiff == 0 ? 'normal' : 'bold';
                    })
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('updated_at', $direction === 'asc' ? 'desc' : 'asc');
                    }),
                TextColumn::make('updated_at')
                    ->label('Last Modified')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('complete_task')
                        ->label('Complete Task')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Complete Task')
                        ->modalDescription('Are you sure you want to mark this task as completed?')
                        ->visible(fn (ResellerHandoverFe $record): bool =>
                            !($record->reseller_payment_completed ?? false) && auth()->user()->role_id !== 2
                        )
                        ->action(function (ResellerHandoverFe $record): void {
                            try {
                                $record->update([
                                    'reseller_payment_completed' => true,
                                ]);

                                Notification::make()
                                    ->title('Task Completed')
                                    ->success()
                                    ->body('Task has been marked as completed successfully.')
                                    ->send();

                                $this->resetTable();
                            } catch (\Exception $e) {
                                Log::error("Error marking FE task as completed for handover {$record->id}: " . $e->getMessage());

                                Notification::make()
                                    ->title('Error')
                                    ->danger()
                                    ->body('Failed to mark task as completed.')
                                    ->send();
                            }
                        }),
                ])->button(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100])
            ->recordClasses(fn (ResellerHandoverFe $record) =>
                (bool)($record->reseller_payment_completed) ? 'success' : null
            )
            ->emptyState(fn () => view('components.empty-state-question'));
    }

    public function openFilesModal($handoverId)
    {
        $this->selectedHandover = ResellerHandoverFe::find($handoverId);

        if ($this->selectedHandover) {
            $this->handoverFiles = $this->selectedHandover->getCategorizedFilesForModal();
            $this->showFilesModal = true;
        }
    }

    public function closeFilesModal()
    {
        $this->showFilesModal = false;
        $this->selectedHandover = null;
        $this->handoverFiles = [];
    }

    public function render()
    {
        return view('livewire.admin-finance-dashboard.fe-handover-pending-payment-table');
    }
}

<?php

namespace App\Livewire\FinanceInvoice;

use App\Models\FinanceInvoice;
use App\Models\ResellerHandover;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\Attributes\On;

class GenerateInvoiceResellerPortal extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected $listeners = ['refresh-finance-invoice-tables' => '$refresh'];

    public $selectedUser;
    public $lastRefreshTime;

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

    #[On('refresh-softwarehandover-tables')]
    public function refreshData()
    {
        $this->resetTable();
        $this->lastRefreshTime = now()->format('Y-m-d H:i:s');
    }

    #[On('updateTablesForUser')] // Listen for updates
    public function updateTablesForUser($selectedUser)
    {
        $this->selectedUser = $selectedUser;
        session(['selectedUser' => $selectedUser]); // Store for consistency

        $this->resetTable(); // Refresh the table
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyState(fn () => view('components.empty-state-question'))
            ->query(
                FinanceInvoice::where('portal_type', 'reseller')
                    ->with(['resellerHandover', 'creator'])
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('formatted_id')
                    ->label('ID')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('id', $direction);
                    })
                    ->weight('bold')
                    ->color('primary')
                    ->url(fn (FinanceInvoice $record) => route('pdf.print-finance-invoice', $record->id))
                    ->openUrlInNewTab(),

                TextColumn::make('autocount_invoice_number')
                    ->label('Invoice Number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reseller_name')
                    ->label('Reseller Name')
                    ->searchable()
                    ->wrap()
                    ->sortable(),

                TextColumn::make('subscriber_name')
                    ->label('Subscriber Name')
                    ->searchable()
                    ->wrap()
                    ->sortable(),

                TextColumn::make('reseller_commission_amount')
                    ->label('Amount')
                    ->formatStateUsing(function ($state, $record) {
                        $currency = $record->currency ?? 'MYR';
                        return $currency . ' ' . number_format($state, 2);
                    })
                    ->sortable(),
            ])
            ->actions([
                Action::make('view_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (FinanceInvoice $record): string => route('pdf.print-finance-invoice', $record))
                    ->openUrlInNewTab(),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Generate Invoice')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Select::make('reseller_handover_id')
                            ->label('Reseller Portal')
                            ->options(function () {
                                return ResellerHandover::query()
                                    ->where('status', 'pending_timetec_invoice')
                                    ->get()
                                    // ->filter(function ($handover) {
                                    //     $resellerName = strtoupper($handover->reseller_company_name ?? $handover->reseller_name ?? '');
                                    //     $subscriberName = strtoupper($handover->subscriber_name ?? '');

                                    //     // Check if this combination already exists in FinanceInvoice
                                    //     return !FinanceInvoice::where('reseller_name', $resellerName)
                                    //         ->where('subscriber_name', $subscriberName)
                                    //         ->exists();
                                    // })
                                    ->mapWithKeys(function ($handover) {
                                        $resellerName = strtoupper($handover->reseller_company_name ?? $handover->reseller_name ?? 'Unknown Reseller');
                                        $subscriberName = strtoupper($handover->subscriber_name ?? 'Unknown Subscriber');
                                        return [$handover->id => "{$handover->fb_id} - {$resellerName} - {$subscriberName}"];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $handover = ResellerHandover::find($state);
                                    if ($handover) {
                                        $resellerName = strtoupper($handover->reseller_company_name ?? $handover->reseller_name ?? '');
                                        $set('reseller_name', $resellerName);
                                        $set('subscriber_name', strtoupper($handover->subscriber_name ?? ''));

                                        // Get currency from reseller's debtor/creditor code prefix
                                        $reseller = \App\Models\ResellerV2::where('company_name', $handover->reseller_company_name)->first();
                                        $debtorCode = $reseller?->debtor_code ?? '';
                                        $creditorCode = $reseller?->creditor_code ?? '';
                                        $isUsd = str_starts_with($debtorCode, 'ARU') || str_starts_with($creditorCode, 'APU');
                                        $currency = $isUsd ? 'USD' : 'MYR';
                                        $set('currency', $currency);
                                        $set('currency_rate', $currency === 'MYR' ? '1.0000' : null);
                                    }
                                }
                            }),

                        Hidden::make('currency')
                            ->default('MYR'),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('reseller_name')
                                    ->label('Reseller Name')
                                    ->disabled()
                                    ->dehydrated(true),

                                TextInput::make('autocount_invoice_number')
                                    ->label('AutoCount Invoice Number')
                                    ->required()
                                    ->default('ERIN')
                                    ->extraAlpineAttributes([
                                        'x-on:input' => '
                                            const start = $el.selectionStart;
                                            const end = $el.selectionEnd;
                                            const value = $el.value;
                                            $el.value = value.toUpperCase();
                                            $el.setSelectionRange(start, end);
                                        '
                                    ])
                                    ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                                    ->minLength(13)
                                    ->maxLength(13)
                                    ->rules([
                                        'regex:/^ERIN/i',
                                    ])
                                    ->validationMessages([
                                        'min' => 'The AutoCount Invoice Number field must be at least 13 characters.',
                                        'max' => 'The AutoCount Invoice Number field must not exceed 13 characters.',
                                        'required' => 'The AutoCount Invoice Number field is required.',
                                        'regex' => 'The AutoCount Invoice Number must start with ERIN.',
                                    ])
                                    ->rule(function () {
                                        return function (string $attribute, $value, \Closure $fail) {
                                            if (!$value) {
                                                return;
                                            }

                                            $exists = \App\Models\FinanceInvoice::where('autocount_invoice_number', strtoupper($value))->exists();

                                            if ($exists) {
                                                $fail('This AutoCount Invoice Number already exists.');
                                            }
                                        };
                                    }),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('subscriber_name')
                                    ->label('Subscriber Name')
                                    ->disabled()
                                    ->dehydrated(true),

                                TextInput::make('reseller_commission_amount')
                                    ->label('Reseller Commission Amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix(fn ($get) => $get('currency') ?? 'RM')
                                    ->step('0.01'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('currency')
                                    ->label('Currency')
                                    ->disabled()
                                    ->dehydrated(true)
                                    ->default('MYR'),

                                TextInput::make('currency_rate')
                                    ->label('Currency Rate')
                                    ->required()
                                    ->numeric()
                                    ->step('0.0001')
                                    ->default('1.0000'),
                            ]),
                    ])
                    ->action(function (array $data): void {
                        try {
                            $resellerHandover = \App\Models\ResellerHandover::find($data['reseller_handover_id']);

                            $invoice = FinanceInvoice::create([
                                'fc_number' => FinanceInvoice::generateFcNumber('reseller', $data['autocount_invoice_number'] ?? null),
                                'reseller_handover_id' => $data['reseller_handover_id'],
                                'autocount_invoice_number' => $data['autocount_invoice_number'],
                                'timetec_invoice_number' => $resellerHandover?->timetec_proforma_invoice,
                                'reseller_name' => $data['reseller_name'],
                                'subscriber_name' => $data['subscriber_name'],
                                'reseller_commission_amount' => $data['reseller_commission_amount'],
                                'portal_type' => 'reseller',
                                'created_by' => auth()->id(),
                                'status'    => 'new',
                                'currency' => $data['currency'] ?? 'MYR',
                                'currency_rate' => $data['currency_rate'] ?? 1.00,
                            ]);

                            Notification::make()
                                ->title('Invoice Generated')
                                ->success()
                                ->body('Finance invoice has been generated successfully.')
                                ->send();

                            $this->dispatch('refresh-finance-invoice-tables');

                            // Redirect to open PDF in new tab
                            $this->js('window.open("' . route('pdf.print-finance-invoice', $invoice) . '", "_blank")');
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->danger()
                                ->body('Failed to generate invoice: ' . $e->getMessage())
                                ->send();
                        }
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('300s');
    }

    public function render()
    {
        return view('livewire.finance-invoice.generate-invoice-reseller-portal');
    }
}

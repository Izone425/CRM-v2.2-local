<?php

namespace App\Livewire\FinanceInvoice;

use App\Models\FinanceInvoice;
use App\Models\ResellerHandover;
use App\Models\CrmInvoiceDetail;
use App\Mail\FinanceInvoiceNotification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\Attributes\On;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Mail;

class GenerateInvoiceAdminPortal extends Component implements HasTable, HasForms
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
                FinanceInvoice::where('portal_type', 'admin')
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
                    ->label('AC Invoice')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('timetec_invoice_number')
                    ->label('TT Invoice')
                    ->searchable()
                    ->sortable()
                    ->url(function ($record) {
                        if (!$record->timetec_invoice_number) {
                            return null;
                        }

                        // Get the CrmInvoiceDetail record based on timetec_invoice_number
                        $crmInvoice = CrmInvoiceDetail::where('f_invoice_no', $record->timetec_invoice_number)->first();

                        if (!$crmInvoice) {
                            return null;
                        }

                        $aesKey = 'Epicamera@99';
                        try {
                            $encrypted = openssl_encrypt($crmInvoice->f_id, "AES-128-ECB", $aesKey);
                            $encryptedBase64 = base64_encode($encrypted);
                            return 'https://www.timeteccloud.com/paypal_reseller_invoice?iIn=' . $encryptedBase64;
                        } catch (\Exception $e) {
                            return null;
                        }
                    })
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->weight('bold'),

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
                ActionGroup::make([
                    Action::make('view_pdf')
                        ->label('PDF')
                        ->icon('heroicon-o-document-text')
                        ->url(fn (FinanceInvoice $record): string => route('pdf.print-finance-invoice', $record))
                        ->openUrlInNewTab(),

                    Action::make('export_purchase_invoice')
                        ->label('Export Purchase Invoice')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->url(fn (FinanceInvoice $record): string => route('finance-purchase-invoice.export', $record->id))
                        ->openUrlInNewTab(),
                ])->button(),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Generate Invoice')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Select::make('crm_invoice_detail_id')
                            ->label('Admin Portal')
                            ->options(function () {
                                return CrmInvoiceDetail::query()
                                    ->pendingInvoices()
                                    ->get()
                                    ->filter(function ($invoice) {
                                        // Exclude if this invoice number already exists in FinanceInvoice
                                        return !FinanceInvoice::where('timetec_invoice_number', $invoice->f_invoice_no)
                                            ->exists();
                                    })
                                    ->mapWithKeys(function ($invoice) {
                                        $companyName = strtoupper($invoice->subscriber_name ?? 'Unknown Company');
                                        $subscriberName = strtoupper($invoice->company_name ?? 'Not Available');
                                        $amount = number_format($invoice->f_total_amount, 2);
                                        return [$invoice->f_id => "{$invoice->f_invoice_no} - {$companyName} - {$subscriberName} - {$invoice->f_currency} {$amount}"];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                if ($state) {
                                    $invoice = CrmInvoiceDetail::query()
                                        ->pendingInvoices()
                                        ->havingRaw('MIN(crm_invoice_details.f_id) = ?', [$state])
                                        ->first();

                                    if ($invoice) {
                                        $set('reseller_name', strtoupper($invoice->subscriber_name ?? ''));
                                        $set('subscriber_name', strtoupper($invoice->company_name ?? ''));
                                        $set('timetec_invoice_number', strtoupper($invoice->f_invoice_no ?? ''));
                                        $set('currency', $invoice->f_currency ?? 'MYR');

                                        // Set currency rate only for MYR
                                        if ($invoice->f_currency === 'MYR') {
                                            $set('currency_rate', '1.00');
                                        } else {
                                            $set('currency_rate', null);
                                        }
                                    }
                                }
                            }),

                        Hidden::make('currency')
                            ->default('MYR'),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('timetec_invoice_number')
                                    ->label('TimeTec Invoice Number')
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
                                TextInput::make('reseller_name')
                                    ->label('Reseller Name')
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
                                TextInput::make('subscriber_name')
                                    ->label('Subscriber Name')
                                    ->disabled()
                                    ->dehydrated(true),

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
                            // Find matching reseller handover based on timetec invoice number and subscriber name
                            $resellerHandover = null;
                            if (!empty($data['timetec_invoice_number'])) {
                                $resellerHandover = ResellerHandover::where('timetec_proforma_invoice', $data['timetec_invoice_number'])
                                    ->where('subscriber_name', $data['subscriber_name'])
                                    ->first();
                            }

                            $invoice = FinanceInvoice::create([
                                'fc_number' => FinanceInvoice::generateFcNumber('admin', $data['autocount_invoice_number'] ?? null),
                                'reseller_handover_id' => $resellerHandover?->id,
                                'autocount_invoice_number' => $data['autocount_invoice_number'],
                                'timetec_invoice_number' => $data['timetec_invoice_number'] ?? null,
                                'reseller_name' => $data['reseller_name'],
                                'subscriber_name' => $data['subscriber_name'],
                                'reseller_commission_amount' => $data['reseller_commission_amount'],
                                'portal_type' => 'admin',
                                'status' => (float) $data['reseller_commission_amount'] == 0 ? 'completed' : 'new',
                                'created_by' => auth()->id(),
                                'currency' => $data['currency'] ?? 'MYR',
                                'currency_rate' => $data['currency_rate'] ?? 1.00,
                            ]);

                            // Send email notification
                            Mail::to('faiz@timeteccloud.com')
                                ->send(new FinanceInvoiceNotification($invoice));

                            Notification::make()
                                ->title('Invoice Generated')
                                ->success()
                                ->body('Finance invoice has been generated successfully.')
                                ->send();

                            $this->dispatch('refresh-finance-invoice-tables');

                            // Open PDF in new tab
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
        return view('livewire.finance-invoice.generate-invoice-admin-portal');
    }
}

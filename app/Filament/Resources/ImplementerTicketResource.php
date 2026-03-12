<?php

namespace App\Filament\Resources;

use App\Enums\ImplementerTicketStatus;
use App\Filament\Resources\ImplementerTicketResource\Pages;
use App\Filament\Resources\ImplementerTicketResource\RelationManagers;
use App\Models\ImplementerTicket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ImplementerTicketResource extends Resource
{
    protected static ?string $model = ImplementerTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Implementer';
    protected static ?string $navigationLabel = 'Ticketing';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user || !($user instanceof User)) {
            return false;
        }
        return $user->hasRouteAccess('filament.admin.resources.implementer-tickets.index');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(5)
                            ->columnSpan(2),

                        Forms\Components\Select::make('status')
                            ->options(collect(ImplementerTicketStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                            ->required()
                            ->default('open'),

                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->required()
                            ->default('medium'),

                        Forms\Components\Select::make('category')
                            ->options([
                                'Technical' => 'Technical',
                                'Configuration' => 'Configuration',
                                'Training' => 'Training',
                                'Bug' => 'Bug',
                                'Other' => 'Other',
                            ]),

                        Forms\Components\Select::make('implementer_user_id')
                            ->label('Assigned Implementer')
                            ->options(User::pluck('name', 'id'))
                            ->searchable(),

                        Forms\Components\FileUpload::make('attachments')
                            ->multiple()
                            ->directory('implementer-tickets')
                            ->columnSpan(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('Ticket #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('customer.company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ImplementerTicketStatus $state) => $state->label())
                    ->color(fn (ImplementerTicketStatus $state) => $state->color())
                    ->sortable(),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'low' => 'gray',
                        'medium' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('category')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('implementerUser.name')
                    ->label('Assigned To')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('replies_count')
                    ->label('Replies')
                    ->counts('replies')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(ImplementerTicketStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),

                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),

                Tables\Filters\SelectFilter::make('implementer_user_id')
                    ->label('Implementer')
                    ->options(User::pluck('name', 'id'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('changeStatus')
                    ->label('Status')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options(collect(ImplementerTicketStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                            ->required(),
                    ])
                    ->action(function (ImplementerTicket $record, array $data) {
                        $record->update(['status' => $data['status']]);

                        if ($data['status'] === 'closed') {
                            $record->update([
                                'closed_at' => now(),
                                'closed_by' => auth()->id(),
                                'closed_by_type' => 'user',
                            ]);
                        }
                    }),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Ticket Details')
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('ticket_number')
                            ->label('Ticket #')
                            ->weight('bold'),

                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (ImplementerTicketStatus $state) => $state->label())
                            ->color(fn (ImplementerTicketStatus $state) => $state->color()),

                        Infolists\Components\TextEntry::make('priority')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {
                                'low' => 'gray',
                                'medium' => 'info',
                                'high' => 'warning',
                                'urgent' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state) => ucfirst($state)),

                        Infolists\Components\TextEntry::make('customer.name')
                            ->label('Customer'),

                        Infolists\Components\TextEntry::make('customer.company_name')
                            ->label('Company'),

                        Infolists\Components\TextEntry::make('implementerUser.name')
                            ->label('Assigned To'),

                        Infolists\Components\TextEntry::make('category'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime('d M Y H:i'),

                        Infolists\Components\TextEntry::make('closed_at')
                            ->dateTime('d M Y H:i')
                            ->placeholder('Not closed'),
                    ]),

                Infolists\Components\Section::make('Content')
                    ->schema([
                        Infolists\Components\TextEntry::make('subject')
                            ->weight('bold'),

                        Infolists\Components\TextEntry::make('description')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RepliesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListImplementerTickets::route('/'),
            'view' => Pages\ViewImplementerTicket::route('/{record}'),
        ];
    }
}

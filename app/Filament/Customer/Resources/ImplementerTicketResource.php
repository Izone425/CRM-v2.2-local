<?php

namespace App\Filament\Customer\Resources;

use App\Enums\ImplementerTicketStatus;
use App\Filament\Customer\Resources\ImplementerTicketResource\Pages;
use App\Filament\Customer\Resources\ImplementerTicketResource\RelationManagers;
use App\Models\ImplementerTicket;
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

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Support Tickets';
    protected static ?string $modelLabel = 'Support Ticket';
    protected static ?string $pluralModelLabel = 'Support Tickets';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('customer_id', auth()->guard('customer')->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('subject')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),

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

                Forms\Components\FileUpload::make('attachments')
                    ->multiple()
                    ->directory('implementer-tickets')
                    ->columnSpanFull(),
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

                Tables\Columns\TextColumn::make('implementerUser.name')
                    ->label('Assigned To')
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

                        Infolists\Components\TextEntry::make('implementerUser.name')
                            ->label('Assigned To'),

                        Infolists\Components\TextEntry::make('category'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime('d M Y H:i'),
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
            'create' => Pages\CreateImplementerTicket::route('/create'),
            'view' => Pages\ViewImplementerTicket::route('/{record}'),
        ];
    }
}

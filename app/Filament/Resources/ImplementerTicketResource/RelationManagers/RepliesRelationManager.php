<?php

namespace App\Filament\Resources\ImplementerTicketResource\RelationManagers;

use App\Models\ImplementerTicketReply;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RepliesRelationManager extends RelationManager
{
    protected static string $relationship = 'replies';

    protected static ?string $title = 'Conversation';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sender_type')
                    ->label('From')
                    ->formatStateUsing(fn (ImplementerTicketReply $record) => $record->getSenderTypeLabel())
                    ->badge()
                    ->color(fn (ImplementerTicketReply $record) => match ($record->sender_type) {
                        'App\Models\Customer' => 'info',
                        'App\Models\User' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('sender.name')
                    ->label('Name'),

                Tables\Columns\TextColumn::make('message')
                    ->wrap()
                    ->limit(200),

                Tables\Columns\IconColumn::make('is_internal_note')
                    ->label('Internal')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'asc')
            ->paginated(false);
    }
}

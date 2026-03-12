<?php

namespace App\Filament\Resources\ImplementerTicketResource\Pages;

use App\Enums\ImplementerTicketStatus;
use App\Filament\Resources\ImplementerTicketResource;
use App\Models\ImplementerTicket;
use App\Models\ImplementerTicketReply;
use App\Notifications\ImplementerTicketNotification;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;

class ViewImplementerTicket extends ViewRecord
{
    protected static string $resource = ImplementerTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('changeStatus')
                ->label('Change Status')
                ->icon('heroicon-o-arrow-path')
                ->form([
                    Forms\Components\Select::make('status')
                        ->options(collect(ImplementerTicketStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                        ->required()
                        ->default(fn () => $this->record->status->value),
                ])
                ->action(function (array $data) {
                    $this->record->update(['status' => $data['status']]);

                    if ($data['status'] === 'closed') {
                        $this->record->update([
                            'closed_at' => now(),
                            'closed_by' => auth()->id(),
                            'closed_by_type' => 'user',
                        ]);

                        // Notify customer of closure
                        $this->record->customer?->notify(
                            new ImplementerTicketNotification($this->record, 'closed', auth()->user()->name)
                        );
                    } else {
                        // Notify customer of status change
                        $this->record->customer?->notify(
                            new ImplementerTicketNotification($this->record, 'status_changed', auth()->user()->name)
                        );
                    }
                }),

            Actions\Action::make('addReply')
                ->label('Add Reply')
                ->icon('heroicon-o-chat-bubble-left')
                ->form([
                    Forms\Components\Textarea::make('message')
                        ->required()
                        ->rows(4),

                    Forms\Components\FileUpload::make('attachments')
                        ->multiple()
                        ->directory('implementer-ticket-replies'),

                    Forms\Components\Toggle::make('is_internal_note')
                        ->label('Internal Note (not visible to customer)')
                        ->default(false),
                ])
                ->action(function (array $data) {
                    ImplementerTicketReply::create([
                        'implementer_ticket_id' => $this->record->id,
                        'sender_type' => 'App\Models\User',
                        'sender_id' => auth()->id(),
                        'message' => $data['message'],
                        'attachments' => $data['attachments'] ?? null,
                        'is_internal_note' => $data['is_internal_note'] ?? false,
                    ]);

                    // Track first response time
                    if (!$data['is_internal_note'] && !$this->record->first_responded_at) {
                        $this->record->update(['first_responded_at' => now()]);
                    }

                    // Auto-status: implementer replies → pending_client
                    if (!$data['is_internal_note'] && $this->record->status !== ImplementerTicketStatus::CLOSED) {
                        $this->record->update(['status' => ImplementerTicketStatus::PENDING_CLIENT->value]);

                        // Notify customer
                        $this->record->customer?->notify(
                            new ImplementerTicketNotification($this->record, 'replied_by_implementer', auth()->user()->name)
                        );
                    }
                }),
        ];
    }
}

<?php

namespace App\Filament\Customer\Resources\ImplementerTicketResource\Pages;

use App\Enums\ImplementerTicketStatus;
use App\Filament\Customer\Resources\ImplementerTicketResource;
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
            Actions\Action::make('addReply')
                ->label('Reply')
                ->icon('heroicon-o-chat-bubble-left')
                ->visible(fn () => $this->record->status !== ImplementerTicketStatus::CLOSED)
                ->form([
                    Forms\Components\Textarea::make('message')
                        ->required()
                        ->rows(4),

                    Forms\Components\FileUpload::make('attachments')
                        ->multiple()
                        ->directory('implementer-ticket-replies'),
                ])
                ->action(function (array $data) {
                    $customer = auth()->guard('customer')->user();

                    ImplementerTicketReply::create([
                        'implementer_ticket_id' => $this->record->id,
                        'sender_type' => 'App\Models\Customer',
                        'sender_id' => $customer->id,
                        'message' => $data['message'],
                        'attachments' => $data['attachments'] ?? null,
                        'is_internal_note' => false,
                    ]);

                    // Auto-status: customer replies → pending_support
                    if ($this->record->status !== ImplementerTicketStatus::CLOSED) {
                        $this->record->update(['status' => ImplementerTicketStatus::PENDING_SUPPORT->value]);
                    }

                    // Notify implementer
                    if ($this->record->implementerUser) {
                        $this->record->implementerUser->notify(
                            new ImplementerTicketNotification($this->record, 'replied_by_customer', $customer->name)
                        );
                    }
                }),
        ];
    }
}

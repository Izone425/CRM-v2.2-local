<?php

namespace App\Filament\Customer\Resources\ImplementerTicketResource\Pages;

use App\Filament\Customer\Resources\ImplementerTicketResource;
use App\Models\Customer;
use App\Models\ImplementerTicket;
use App\Notifications\ImplementerTicketNotification;
use Filament\Resources\Pages\CreateRecord;

class CreateImplementerTicket extends CreateRecord
{
    protected static string $resource = ImplementerTicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $customer = auth()->guard('customer')->user();
        $data['customer_id'] = $customer->id;
        $data['status'] = 'open';

        // Auto-assign implementer
        $resolved = ImplementerTicket::resolveImplementerForCustomer($customer);

        if ($resolved) {
            $data['implementer_user_id'] = $resolved['user']->id;
            $data['implementer_name'] = $resolved['name'];
            $data['lead_id'] = $resolved['lead_id'];
            $data['software_handover_id'] = $resolved['software_handover_id'];
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $ticket = $this->record;

        // Notify the assigned implementer
        if ($ticket->implementerUser) {
            $customerName = $ticket->customer?->name ?? 'A customer';
            $ticket->implementerUser->notify(
                new ImplementerTicketNotification($ticket, 'created', $customerName)
            );
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

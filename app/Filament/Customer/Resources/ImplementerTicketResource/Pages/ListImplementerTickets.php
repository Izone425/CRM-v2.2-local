<?php

namespace App\Filament\Customer\Resources\ImplementerTicketResource\Pages;

use App\Filament\Customer\Resources\ImplementerTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImplementerTickets extends ListRecords
{
    protected static string $resource = ImplementerTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Ticket'),
        ];
    }
}

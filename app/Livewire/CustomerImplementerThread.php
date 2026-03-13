<?php

namespace App\Livewire;

use App\Enums\ImplementerTicketStatus;
use App\Models\ImplementerTicket;
use Livewire\Component;

class CustomerImplementerThread extends Component
{
    public function getTickets()
    {
        $customer = auth('customer')->user();

        if (!$customer || !$customer->lead_id) {
            return collect();
        }

        return ImplementerTicket::where('lead_id', $customer->lead_id)
            ->with(['customer', 'replies'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.customer-implementer-thread', [
            'tickets' => $this->getTickets(),
        ]);
    }
}

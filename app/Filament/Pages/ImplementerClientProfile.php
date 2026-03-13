<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Models\ImplementerTicket;
use App\Models\User;
use Filament\Pages\Page;

class ImplementerClientProfile extends Page
{
    protected static string $view = 'filament.pages.implementer-client-profile';
    protected static ?string $title = '';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'implementer-client-profile/{customerId}';

    public $customerId;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user || !($user instanceof User)) {
            return false;
        }
        return $user->hasRouteAccess('filament.admin.pages.implementer-ticketing-dashboard');
    }

    public function mount($customerId)
    {
        $this->customerId = $customerId;

        $customer = Customer::find($customerId);
        if (!$customer) {
            abort(404);
        }
    }

    public function getViewData(): array
    {
        $customer = Customer::find($this->customerId);

        $tickets = ImplementerTicket::with(['customer', 'implementerUser'])
            ->where('customer_id', $this->customerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'customer' => $customer,
            'tickets' => $tickets,
            'ticketCount' => $tickets->count(),
        ];
    }
}

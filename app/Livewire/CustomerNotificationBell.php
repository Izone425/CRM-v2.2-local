<?php

namespace App\Livewire;

use Livewire\Component;

class CustomerNotificationBell extends Component
{
    public function getUnreadCountProperty(): int
    {
        $customer = auth()->guard('customer')->user();
        if (!$customer) return 0;

        return $customer->unreadNotifications()->count();
    }

    public function getNotificationsProperty()
    {
        $customer = auth()->guard('customer')->user();
        if (!$customer) return collect();

        return $customer->notifications()->latest()->take(15)->get();
    }

    public function markAsRead($notificationId)
    {
        $customer = auth()->guard('customer')->user();
        if (!$customer) return;

        $notification = $customer->notifications()->where('id', $notificationId)->first();
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        $customer = auth()->guard('customer')->user();
        if (!$customer) return;

        $customer->unreadNotifications->markAsRead();
    }

    public function openNotification($notificationId)
    {
        $customer = auth()->guard('customer')->user();
        if (!$customer) return;

        $notification = $customer->notifications()->where('id', $notificationId)->first();
        if (!$notification) return;

        $notification->markAsRead();

        $data = $notification->data;
        $ticketId = $data['entity_id'] ?? null;

        if ($ticketId) {
            return $this->redirect('/customer/dashboard?tab=impThread&ticket=' . $ticketId);
        }
    }

    public function render()
    {
        return view('livewire.customer-notification-bell');
    }
}

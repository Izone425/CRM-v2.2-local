<?php

namespace App\Notifications;

use App\Models\ImplementerTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ImplementerTicketNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;
    public $action;
    public $actionByName;
    public $additionalData;

    public function __construct(ImplementerTicket $ticket, string $action, string $actionByName = 'Someone', array $additionalData = [])
    {
        $this->ticket = $ticket;
        $this->action = $action;
        $this->actionByName = $actionByName;
        $this->additionalData = $additionalData;
    }

    public function via($notifiable): array
    {
        $config = config("notification-scenarios.implementer_ticket.{$this->action}");
        $channels = $config['channels'] ?? [];

        $result = [];
        if (in_array('in_app', $channels)) {
            $result[] = 'database';
        }
        if (in_array('email', $channels)) {
            $result[] = 'mail';
        }

        return $result;
    }

    public function toDatabase($notifiable): array
    {
        $config = config("notification-scenarios.implementer_ticket.{$this->action}");

        return [
            'type' => 'implementer_ticket.' . $this->action,
            'title' => $this->parseTemplate($config['title'] ?? 'Implementer Ticket Update'),
            'message' => $this->parseTemplate($config['message'] ?? 'Ticket {ticket_number} updated'),
            'action_url' => $this->parseTemplate($config['action_url'] ?? '/implementer-tickets/{ticket_id}'),
            'priority' => $config['priority'] ?? 'normal',
            'entity_type' => 'implementer_ticket',
            'entity_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'action_by' => $this->actionByName,
            'metadata' => $this->additionalData,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $config = config("notification-scenarios.implementer_ticket.{$this->action}");
        $emailConfig = $config['email'] ?? [];

        return (new MailMessage)
            ->subject($this->parseTemplate($emailConfig['subject'] ?? 'Implementer Ticket Update - {ticket_number}'))
            ->greeting($emailConfig['greeting'] ?? 'Hello!')
            ->line($this->parseTemplate($config['message'] ?? 'Ticket {ticket_number} has been updated.'))
            ->action(
                $emailConfig['button_text'] ?? 'View Ticket',
                url($this->parseTemplate($config['action_url'] ?? '/implementer-tickets/{ticket_id}'))
            )
            ->line('Thank you for using TimeTec CRM.');
    }

    private function parseTemplate(string $template): string
    {
        $replacements = [
            '{ticket_id}' => $this->ticket->id,
            '{ticket_number}' => $this->ticket->ticket_number ?? '',
            '{subject}' => $this->ticket->subject,
            '{status}' => $this->ticket->status?->label() ?? '',
            '{priority}' => ucfirst($this->ticket->priority ?? ''),
            '{customer_name}' => $this->ticket->customer?->name ?? 'Unknown',
            '{company_name}' => $this->ticket->customer?->company_name ?? 'Unknown',
            '{implementer_name}' => $this->ticket->implementerUser?->name ?? $this->ticket->implementer_name ?? 'Unassigned',
            '{action_by}' => $this->actionByName,
        ];

        foreach ($this->additionalData as $key => $value) {
            $replacements['{' . $key . '}'] = is_string($value) ? $value : json_encode($value);
        }

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );
    }
}

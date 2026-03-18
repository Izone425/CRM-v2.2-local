<?php

namespace App\Mail;

use App\Models\ImplementerTicket;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ImplementerTicketHrNotification extends Mailable
{
    use SerializesModels;

    public ImplementerTicket $ticket;
    public string $action;
    public string $actionByName;
    public string $portalUrl;
    public string $actionLabel;
    public string $descriptionPreview;

    private const ACTION_LABELS = [
        'created' => 'New Support Ticket Created',
        'replied_by_implementer' => 'New Reply on Your Ticket',
        'status_changed' => 'Ticket Status Updated',
        'closed' => 'Ticket Closed',
        'merged' => 'Ticket Merged',
    ];

    private const ACTION_SUBJECTS = [
        'created' => '[TimeTec CRM] New Support Ticket Created - {ticket_number}',
        'replied_by_implementer' => '[TimeTec CRM] New Reply on Ticket - {ticket_number}',
        'status_changed' => '[TimeTec CRM] Ticket Status Updated - {ticket_number}',
        'closed' => '[TimeTec CRM] Ticket Closed - {ticket_number}',
        'merged' => '[TimeTec CRM] Ticket Merged - {ticket_number}',
    ];

    public function __construct(ImplementerTicket $ticket, string $action, string $actionByName)
    {
        $this->ticket = $ticket;
        $this->action = $action;
        $this->actionByName = $actionByName;
        $this->portalUrl = url('/customer/dashboard?tab=impThread&ticket=' . $ticket->id);
        $this->actionLabel = self::ACTION_LABELS[$action] ?? 'Ticket Update';
        $this->descriptionPreview = $this->buildDescriptionPreview();
    }

    public function envelope(): Envelope
    {
        $subject = str_replace(
            '{ticket_number}',
            $this->ticket->ticket_number ?? $this->ticket->formatted_ticket_number ?? '',
            self::ACTION_SUBJECTS[$this->action] ?? '[TimeTec CRM] Ticket Update - {ticket_number}'
        );

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.implementer_ticket_hr_notification',
        );
    }

    private function buildDescriptionPreview(): string
    {
        $description = $this->ticket->description ?? '';
        $stripped = strip_tags($description);
        $stripped = html_entity_decode($stripped, ENT_QUOTES, 'UTF-8');
        $stripped = preg_replace('/\s+/', ' ', trim($stripped));

        if (mb_strlen($stripped) > 200) {
            return mb_substr($stripped, 0, 200) . '...';
        }

        return $stripped;
    }
}

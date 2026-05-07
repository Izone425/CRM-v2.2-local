<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ImplementerThreadNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $emailSubject,
        public string $portalUrl,
        public string $implementerName,
        public string $implementerDesignation = 'Implementer',
        public string $implementerCompany = 'TimeTec Cloud Sdn Bhd',
        public string $implementerPhone = '',
        public string $implementerEmail = '',
        public string $senderEmail = '',
        public string $senderName = '',
    ) {}

    public function envelope(): Envelope
    {
        $envelope = new Envelope(subject: $this->emailSubject);

        if (!empty($this->senderEmail)) {
            $envelope = new Envelope(
                from: new \Illuminate\Mail\Mailables\Address($this->senderEmail, $this->senderName ?: $this->senderEmail),
                subject: $this->emailSubject,
            );
        }

        return $envelope;
    }

    public function content(): Content
    {
        return new Content(view: 'emails.implementer-thread-notification');
    }
}

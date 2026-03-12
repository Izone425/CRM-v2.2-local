<?php

namespace App\Mail;

use App\Models\FinanceInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FinanceInvoiceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public FinanceInvoice $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct(FinanceInvoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SELF BILLED E INVOICE - TIMETEC HR',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.finance-invoice-notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

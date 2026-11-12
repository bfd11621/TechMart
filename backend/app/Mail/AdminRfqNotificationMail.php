<?php

namespace App\Mail;

use App\Models\RfqRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminRfqNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public RfqRequest $rfq)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[New RFQ Alert] {$this->rfq->rfq_number} — {$this->rfq->customer_name} ({$this->rfq->company_name})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_rfq_notification',
        );
    }
}

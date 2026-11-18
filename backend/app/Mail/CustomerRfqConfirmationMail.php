<?php

namespace App\Mail;

use App\Models\RfqRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerRfqConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public RfqRequest $rfq)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('dagicreative16@gmail.com', 'iRACK IT Solution'),
            replyTo: [new Address('dagicreative16@gmail.com', 'iRACK IT Solution')],
            subject: "Quotation Request Acknowledgment — Reference: {$this->rfq->rfq_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer_rfq_confirmation',
        );
    }
}

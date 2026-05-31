<?php

namespace App\Mail;

use App\Models\ContactRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactRequestReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactRequest $contactRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Quote Request — '.$this->contactRequest->service,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-request-received',
        );
    }
}

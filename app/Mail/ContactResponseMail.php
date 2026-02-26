<?php

namespace App\Mail;

use App\Models\ContactMessage;
use App\Models\ContactResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $message,
        public ContactResponse $response
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $siteName = \App\Models\HomepageSetting::getSetting('branding', 'site_name') ?? 'LMS Inc';
        return new Envelope(
            subject: 'Response to Your Message - ' . $siteName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-response',
            with: [
                'message' => $this->message,
                'response' => $this->response,
            ],
        );
    }
}

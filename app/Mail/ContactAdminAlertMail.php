<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactAdminAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $message) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Pesan Kontak Baru: {$this->message->subject}");
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.contact-admin-alert', with: [
            'contact' => $this->message,
        ]);
    }
}

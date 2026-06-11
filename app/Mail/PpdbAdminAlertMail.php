<?php

namespace App\Mail;

use App\Models\PpdbRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PpdbAdminAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PpdbRegistration $registration) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Pendaftar PPDB Baru: {$this->registration->full_name}");
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.ppdb-admin-alert', with: [
            'registration' => $this->registration,
        ]);
    }
}

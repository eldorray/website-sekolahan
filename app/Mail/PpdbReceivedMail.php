<?php

namespace App\Mail;

use App\Models\PpdbRegistration;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PpdbReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PpdbRegistration $registration) {}

    public function envelope(): Envelope
    {
        $school = Setting::get('school_name', 'Sekolah');

        return new Envelope(subject: "Konfirmasi Pendaftaran PPDB — {$school}");
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.ppdb-received', with: [
            'registration' => $this->registration,
            'schoolName' => Setting::get('school_name', 'Sekolah'),
        ]);
    }
}

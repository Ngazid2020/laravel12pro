<?php

namespace App\Mail;

use App\Models\CandidatureApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidatureAcceptedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public CandidatureApplication $candidature,
        public string $setupUrl = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Votre candidature a été acceptée — Réseau Entrepreneurs Comores',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.candidature-accepted',
        );
    }
}

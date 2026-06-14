<?php

namespace App\Mail;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentValidatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Paiement validé — Réseau Entrepreneurs Comores',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.payment-validated',
        );
    }

    public function attachments(): array
    {
        $this->payment->load(['user.profile', 'payable', 'validator']);

        $pdf = Pdf::loadView('pdf.receipt', ['payment' => $this->payment])
            ->setPaper('a4', 'portrait');

        $filename = 'recu-'.str_pad($this->payment->id, 6, '0', STR_PAD_LEFT).'.pdf';

        return [
            Attachment::fromData(fn () => $pdf->output(), $filename)
                ->withMime('application/pdf'),
        ];
    }
}

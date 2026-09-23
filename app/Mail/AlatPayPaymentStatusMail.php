<?php

namespace App\Mail;

use App\Models\Payment\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlatPayPaymentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Payment $payment,
        public string $previousStatus,
        public string $eventLabel,
        public array $bookingSummary = [],
        public array $flightSummary = [],
    ) {
    }

    public function envelope(): Envelope
    {
        $ref = $this->payment->reference;
        $status = strtoupper((string) $this->payment->payment_status);

        return new Envelope(
            subject: "ALATPay {$this->eventLabel}: {$status} — {$ref}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alatpay-payment-status',
        );
    }
}

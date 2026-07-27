<?php

namespace App\Mail;

use App\Models\CollectionTracking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CollectionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $companyName;

    public function __construct(
        public CollectionTracking $tracking,
        public $company,
        public ?string $customMessage = null,
    ) {
        $this->companyName = (string) ($company?->name ?? 'Invenza Cash');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recordatorio de pago pendiente - ' . $this->companyName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.collection-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

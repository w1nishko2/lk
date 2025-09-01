<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MassEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public $recipientEmail;

    /**
     * Create a new message instance.
     */
    public function __construct($recipientEmail = null)
    {
        $this->recipientEmail = $recipientEmail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Создайте продающий сайт с WeebS - Скидка 50%!',
            from: config('mail.from.address', 'w1nishko2@yandex.ru'),
            replyTo: [config('mail.from.address', 'w1nishko2@yandex.ru')],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.mass-email',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

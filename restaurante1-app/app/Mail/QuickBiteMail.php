<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuickBiteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $userName;
    public $messageBody;
    public $actionUrl;
    public $actionText;

    /**
     * Create a new message instance.
     */
    public function __construct(string $title, string $userName, string $messageBody, ?string $actionUrl = null, ?string $actionText = null)
    {
        $this->title = $title;
        $this->userName = $userName;
        $this->messageBody = $messageBody;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title . ' | QuickBite Express',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.template',
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

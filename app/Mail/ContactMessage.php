<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name: string, email: string, message: string}  $contactData
     * @param  array{path: string, name: string, mime: ?string}|null  $attachmentData
     */
    public function __construct(
        public array $contactData,
        public ?array $attachmentData = null,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address($this->contactData['email'], $this->contactData['name']),
            ],
            subject: 'New message from contact form',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
            with: [
                'contactData' => $this->contactData,
                'attachmentData' => $this->attachmentData,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if ($this->attachmentData && ! empty($this->attachmentData['path']) && file_exists($this->attachmentData['path'])) {
            $attachment = Attachment::fromPath($this->attachmentData['path'])
                ->as($this->attachmentData['name']);

            if (! empty($this->attachmentData['mime'])) {
                $attachment->withMime($this->attachmentData['mime']);
            }

            return [$attachment];
        }

        return [];
    }
}

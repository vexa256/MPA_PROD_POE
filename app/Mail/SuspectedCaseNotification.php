<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuspectedCaseNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $screeningData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $screeningData)
    {
        $this->screeningData = $screeningData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Alert: ' . $this->screeningData['classification'] . ' - POE Screening Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.screening-alert',
            with: [
                'screeningData' => $this->screeningData,
                'dashboardUrl' => env('POE_DASHBOARD_URL', 'https://poe-dashboard.example.com'),
            ],
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
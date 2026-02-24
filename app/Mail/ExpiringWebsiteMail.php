<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpiringWebsiteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $websites;
    public $threshold;
 
    /**
     * Create a new message instance.
     */
    public function __construct($params, $threshold)
    {
        $this->websites = $params->whereNull('deleted_at');
        $this->threshold = $threshold;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $from = new Address(config('mail.from.address'), config('mail.from.name'));

        return new Envelope(
            from: $from,
            subject: __('mail.Expiring_Websites_Report'),
        );
    }

    /**
     * Get the message content definition.
     */ 
    public function content(): Content
    {
        return new Content(
            markdown: 'notifications.markdown.report-expiring-websites',
            with: [
                'websites'  => $this->websites,
                'threshold' => $this->threshold,
            ]
        );
    }

    /**
     * Attachments (none)
     */
    public function attachments(): array
    {
        return [];
    }
}

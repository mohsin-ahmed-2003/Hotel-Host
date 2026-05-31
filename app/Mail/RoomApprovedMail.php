<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RoomApprovedMail extends Mailable
{
    use SerializesModels;

    public $room;

    /**
     * Create a new message instance.
     */
    public function __construct($room)
    {
        $this->room = $room;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $siteName = \App\Models\SiteSetting::get('site_name', config('app.name'));
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(
                config('mail.from.address', 'noreply@' . request()->getHost()),
                $siteName
            ),
            subject: "Your property \"{$this->room->display_name}\" has been approved! 🎉",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.room_approved',
            with: [
                'siteName' => \App\Models\SiteSetting::get('site_name', config('app.name')),
                'siteLogo' => \App\Models\SiteSetting::getSiteLogoUrl(),
                'userName' => $this->room->user->name ?? 'Host',
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

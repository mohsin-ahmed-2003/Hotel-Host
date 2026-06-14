<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HostReviewNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $review;

    /**
     * Create a new message instance.
     */
    public function __construct(Reservation $reservation, Review $review)
    {
        $this->reservation = $reservation;
        $this->review = $review;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Review Received for ' . ($this->reservation->room->title ?? 'your property'))
                    ->view('emails.host_review_notification');
    }
}

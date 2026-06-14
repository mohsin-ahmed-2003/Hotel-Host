<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Review;

class ReviewReceivedNotification extends Notification
{
    use Queueable;

    public $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'review_received',
            'title' => 'New Guest Review',
            'message' => 'You received a new ' . $this->review->rating . '-star review for ' . ($this->review->room->title ?? 'your room') . '.',
            'url' => route('host.reviews.show', $this->review->reservation_id),
            'icon' => 'fas fa-star',
            'color' => 'var(--accent)'
        ];
    }
}

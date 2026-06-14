<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Reservation;

class ReservationStatusNotification extends Notification
{
    use Queueable;

    public $reservation;
    public $type; // 'new', 'requested', 'cancelled'

    public function __construct(Reservation $reservation, $type)
    {
        $this->reservation = $reservation;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $color = 'var(--accent)';
        $icon = 'fas fa-calendar-alt';
        $title = 'Reservation Update';
        $message = "You have an update for a reservation at {$this->reservation->room->title}.";
        
        if ($this->type === 'new' || $this->type === 'accepted') {
            $title = 'New Reservation Confirmed';
            $message = "Your property '{$this->reservation->room->title}' was booked by {$this->reservation->user->name}.";
            $icon = 'fas fa-calendar-check';
            $color = '#10b981';
        } elseif ($this->type === 'requested') {
            $title = 'New Reservation Request';
            $message = "{$this->reservation->user->name} requested to book '{$this->reservation->room->title}'. Pending your approval.";
            $icon = 'fas fa-calendar-plus';
            $color = '#f59e0b';
        } elseif ($this->type === 'cancelled') {
            $title = 'Reservation Cancelled';
            $message = "A reservation for '{$this->reservation->room->title}' by {$this->reservation->user->name} has been cancelled.";
            $icon = 'fas fa-calendar-times';
            $color = '#ef4444';
        }

        return [
            'type' => 'reservation_status',
            'title' => $title,
            'message' => $message,
            'url' => route('user.reservations.itinerary', $this->reservation->id),
            'icon' => $icon,
            'color' => $color
        ];
    }
}

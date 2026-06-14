<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Room;

class RoomStatusUpdatedNotification extends Notification
{
    use Queueable;

    public $room;
    public $status;
    public $reason;

    public function __construct(Room $room, $status, $reason = null)
    {
        $this->room = $room;
        $this->status = $status;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $color = 'var(--accent)';
        $icon = 'fas fa-info-circle';
        $message = "Your property '{$this->room->title}' status was updated to {$this->status}.";

        if ($this->status === 'approved') {
            $color = '#10b981';
            $icon = 'fas fa-check-circle';
            $message = "Congratulations! Your property '{$this->room->title}' has been approved and is now live.";
        } elseif ($this->status === 'resubmit') {
            $color = '#ef4444';
            $icon = 'fas fa-exclamation-circle';
            $message = "Action Required: Your property '{$this->room->title}' requires resubmission. " . ($this->reason ? "Reason: {$this->reason}" : "");
        }

        return [
            'type' => 'room_status_updated',
            'title' => 'Property Status Updated',
            'message' => $message,
            'url' => route('rooms.show', $this->room->id), // Ensure route exists, otherwise just generic
            'icon' => $icon,
            'color' => $color
        ];
    }
}

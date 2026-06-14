<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminGeneralNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;

    public function __construct($title, $message)
    {
        $this->title = $title;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'admin_general',
            'title' => $this->title,
            'message' => $this->message,
            'url' => '#',
            'icon' => 'fas fa-bullhorn',
            'color' => 'var(--accent)'
        ];
    }
}

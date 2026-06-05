<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClassroomActivityNotification extends Notification
{
    use Queueable;

    protected $classroom;
    protected $activity;
    protected $type;

    public function __construct($classroom, $activity, $type)
    {
        $this->classroom = $classroom;
        $this->activity = $activity;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'classroom_id' => $this->classroom->id,
            'classroom_name' => $this->classroom->title,
            'activity_id' => $this->activity->id,
            'activity_title' => $this->activity->title,
            'type' => $this->type, // 'assignment', 'material', 'announcement'
            'message' => "New " . ucfirst($this->type) . " posted: " . $this->activity->title,
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class AbsenceJustifiedNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct($data)
    {
        $this->id = Str::uuid()->toString();
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'id' => $this->id,
            'type' => 'absence_justified',
            'data' => $this->data,
            'message' => "Absence justifiée pour le cours {$this->data['course_name']} du {$this->data['date']}"
        ];
    }
} 
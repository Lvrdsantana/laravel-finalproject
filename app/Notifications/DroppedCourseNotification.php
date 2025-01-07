<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DroppedCourseNotification extends Notification
{
    protected $student;
    protected $course;

    public function __construct($student, $course)
    {
        $this->student = $student;
        $this->course = $course;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Étudiant droppé - ' . $this->course->name)
            ->line('L\'étudiant ' . $this->student->user->name . ' a été droppé du cours ' . $this->course->name)
            ->line('Taux de présence inférieur à 30%')
            ->action('Voir les détails', url('/attendance/student/' . $this->student->id));
    }
} 
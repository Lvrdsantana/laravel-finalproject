<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\StudentPresence;

class JustificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $presence;

    public function __construct(StudentPresence $presence)
    {
        $this->presence = $presence;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'presence_id' => $this->presence->id,
            'student_id' => $this->presence->student_id,
            'student_name' => $this->presence->student->name,
            'course_name' => $this->presence->timetable->course->name,
            'date' => $this->presence->timetable->date,
            'justification' => $this->presence->justification,
            'message' => "Absence justifiée pour le cours de {$this->presence->timetable->course->name}",
        ];
    }
} 
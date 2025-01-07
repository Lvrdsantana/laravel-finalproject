<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class StudentDroppedNotification extends Notification
{
    use Queueable;

    protected $studentName;
    protected $courseName;
    protected $attendanceRate;

    public function __construct($studentName, $courseName, $attendanceRate)
    {
        $this->id = Str::uuid()->toString();
        $this->studentName = $studentName;
        $this->courseName = $courseName;
        $this->attendanceRate = $attendanceRate;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'id' => $this->id,
            'type' => 'student_dropped',
            'student_name' => $this->studentName,
            'course_name' => $this->courseName,
            'attendance_rate' => $this->attendanceRate,
            'message' => "L'étudiant {$this->studentName} a été droppé du cours {$this->courseName} (Taux de présence: {$this->attendanceRate}%)"
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'id' => $this->id,
            'type' => 'student_dropped',
            'student_name' => $this->studentName,
            'course_name' => $this->courseName,
            'attendance_rate' => $this->attendanceRate
        ];
    }
} 
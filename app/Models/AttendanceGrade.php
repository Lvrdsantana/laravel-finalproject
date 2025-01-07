<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceGrade extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'grade',
        'total_sessions',
        'attended_sessions',
        'semester',
        'academic_year'
    ];

    public function student()
    {
        return $this->belongsTo(Students::class);
    }

    public function course()
    {
        return $this->belongsTo(courses::class);
    }
} 
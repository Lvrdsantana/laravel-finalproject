<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Classe;

class CourseTime extends Model
{
    protected $table = 'course_time';

    protected $fillable = [
        'class_id',
        'course_id',
        'teacher_id',
        'date',
        'start_time',
        'end_time',
    ];

    public function class()
    {
        return $this->belongsTo(classes::class);
    }

    public function course()
    {
        return $this->belongsTo(courses::class);
    }

    public function teacher()
    {
        return $this->belongsTo(teachers::class);
    }
}
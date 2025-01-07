<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'timetable_id',
        'student_id',
        'status',
        'marked_by',
        'marked_at',
         'date'
    ];

    protected $casts = [
        'marked_at' => 'datetime',
        'date' => 'date'
    ];

    public function justification()
    {
        return $this->hasOne(Justification::class);
    }

    public function isJustified()
    {
        return $this->justification()->exists();
    }

    public function timetable()
    {
        return $this->belongsTo(Timetable::class)->withTrashed();
    }

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teachers::class, 'marked_by');
    }
} 
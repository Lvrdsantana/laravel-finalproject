<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimetableHistory extends Model
{
    protected $fillable = [
        'timetable_id',
        'class_id',
        'course_id',
        'teacher_id',
        'day_id',
        'time_slot_id',
        'color',
        'action',
        'modified_by',
        'changes'
    ];

    protected $casts = [
        'changes' => 'array'
    ];

    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Courses::class, 'course_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teachers::class, 'teacher_id');
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(Days::class, 'day_id');
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlots::class, 'time_slot_id');
    }

    public function modifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}

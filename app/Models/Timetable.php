<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timetable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'class_id', 'teacher_id', 'day_id', 'time_slot_id', 'color', 'course_id'
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teachers::class, 'teacher_id');
    }

    public function day()
    {
        return $this->belongsTo(Days::class, 'day_id');
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlots::class, 'time_slot_id');
    }

    public function course()
    {
        return $this->belongsTo(Courses::class, 'course_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TimetableHistory::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($timetable) {
            $timetable->recordHistory('created');
        });

        static::updated(function ($timetable) {
            $changes = $timetable->getChanges();
            unset($changes['updated_at']);
            
            if (!empty($changes)) {
                $timetable->recordHistory('updated', $changes);
            }
        });

        static::deleted(function ($timetable) {
            $timetable->recordHistory('deleted');
        });
    }

    protected function recordHistory(string $action, array $changes = null)
    {
        TimetableHistory::create([
            'timetable_id' => $this->id,
            'class_id' => $this->class_id,
            'course_id' => $this->course_id,
            'teacher_id' => $this->teacher_id,
            'day_id' => $this->day_id,
            'time_slot_id' => $this->time_slot_id,
            'color' => $this->color,
            'action' => $action,
            'modified_by' => Auth::id(),
            'changes' => $changes,
        ]);
    }
}

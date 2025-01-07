<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use App\Models\Students;
use App\Models\Timetable;
use App\Models\Attendance;

class classes extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'description',
    ];

    // Relation avec les étudiants
    public function students()
    {
        return $this->hasMany(Students::class, 'class_id');
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'class_id');
    }

    public function getClassAttendanceRate($startDate = null, $endDate = null)
    {
        $query = Attendance::whereHas('student', function ($q) {
            $q->where('class_id', $this->id);
        });

        if ($startDate && $endDate) {
            $query->whereHas('timetable', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            });
        }

        $totalSessions = $query->count();
        $presentSessions = $query->where('status', 'present')->count();

        return $totalSessions > 0 ? ($presentSessions / $totalSessions) * 100 : 0;
    }
}

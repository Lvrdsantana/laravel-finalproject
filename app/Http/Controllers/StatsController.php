<?php

namespace App\Http\Controllers;

use App\Models\Students;
use App\Models\Classes;
use App\Models\Attendance;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        return view('stats.index');
    }

    public function studentAttendance()
    {
        return view('stats.student-attendance');
    }

    public function classAttendance()
    {
        return view('stats.class-attendance');
    }

    public function courseVolume()
    {
        return view('stats.course-volume');
    }

    public function getStudentAttendanceData()
    {
        $students = Students::with(['attendances', 'user'])->get();
        $data = $students->map(function ($student) {
            $total = $student->attendances->count();
            $present = $student->attendances->where('status', 'present')->count();
            $rate = $total > 0 ? ($present / $total) * 100 : 0;
            
            // Définir la couleur selon le taux
            $color = match(true) {
                $rate >= 70 => '#2E7D32', // Vert foncé
                $rate >= 50.1 => '#4CAF50', // Vert clair
                $rate >= 30.1 => '#FF9800', // Orange
                default => '#F44336', // Rouge
            };

            return [
                'name' => $student->user->name,
                'rate' => round($rate, 1),
                'color' => $color
            ];
        })->sortByDesc('rate')->values();

        return response()->json($data);
    }

    public function getClassAttendanceData()
    {
        $classes = Classes::with('students.attendances')->get();
        $data = $classes->map(function ($class) {
            $totalAttendances = 0;
            $totalPresent = 0;

            foreach ($class->students as $student) {
                $totalAttendances += $student->attendances->count();
                $totalPresent += $student->attendances->where('status', 'present')->count();
            }

            $rate = $totalAttendances > 0 ? ($totalPresent / $totalAttendances) * 100 : 0;

            return [
                'name' => $class->name,
                'rate' => round($rate, 1)
            ];
        });

        return response()->json($data);
    }

    public function getCourseVolumeData()
    {
        $courseVolumes = Timetable::with('course')
            ->select('course_id', DB::raw('count(*) as total_sessions'))
            ->groupBy('course_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->course->name,
                    'sessions' => $item->total_sessions
                ];
            });

        return response()->json($courseVolumes);
    }
} 
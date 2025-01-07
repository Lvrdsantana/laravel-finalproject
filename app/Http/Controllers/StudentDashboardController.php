<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Timetable;
use App\Models\Students;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Days;
use App\Models\TimeSlots;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        $timetables = Timetable::with(['class', 'course', 'teacher.user', 'timeSlot'])
            ->whereHas('class', function($query) use ($student) {
                $query->where('id', $student->class_id);
            })
            ->get();

        return view('StudentDashboard', compact('timetables'));
    }

    public function timetable()
    {
        $student = Auth::user()->student;
        if (!$student) {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        $timetables = Timetable::with(['class', 'course', 'teacher.user', 'timeSlot'])
            ->whereHas('class', function($query) use ($student) {
                $query->where('id', $student->class_id);
            })
            ->get();

        $days = \App\Models\Days::all();
        $time_slots = \App\Models\TimeSlots::all();

        return view('student.timetable', compact('timetables', 'days', 'time_slots'));
    }

    public function profile()
    {
        $student = Auth::user()->student;
        if (!$student) {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        return view('student.profile', compact('student'));
    }
}
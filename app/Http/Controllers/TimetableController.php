<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Timetable;
use App\Models\Classes;
use App\Models\Courses;
use App\Models\Teachers;
use App\Models\User;
use App\Models\Days;
use App\Models\TimeSlots;


class TimetableController extends Controller
{
    // Affiche la liste des emplois du temps
    public function index()
    {
        $classes = \App\Models\classes::all();
        $teachers = \App\Models\Teachers::all();
        $timetables = \App\Models\Timetable::with([
            'class',
            'teacher.user',  // Chargement eager de la relation user du teacher
            'day',
            'timeSlot',
            'course'
        ])->get();
        $days = \App\Models\Days::all();
        $time_slots = \App\Models\TimeSlots::all();
        $courses = \App\Models\courses::all();

        return view('CoordinatorsTimetable', compact(
            'classes',
            'teachers',
            'timetables',
            'days',
            'time_slots',
            'courses'
        ));
    }

    // Crée un nouvel emploi du temps
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required',
            'course_id' => 'required',
            'teacher_id' => 'required',
            'day_id' => 'required',
            'time_slot_id' => 'required',
            'color' => 'required'
        ]);

        Timetable::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Emploi du temps créé avec succès!'
        ]);
    }

    // Met à jour un emploi du temps existant
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'class_id' => 'required',
            'course_id' => 'required',
            'teacher_id' => 'required',
            'day_id' => 'required',
            'time_slot_id' => 'required',
            'color' => 'required'
        ]);

        $timetable = Timetable::findOrFail($id);
        
        $timetable->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Emploi du temps mis à jour avec succès'
        ]);
    }

    // Supprime un emploi du temps
    public function destroy($id)
    {
        $timetable = Timetable::findOrFail($id);
        $timetable->delete();

        return response()->json(['message' => 'Emploi du temps supprimé avec succès']);
    }

    public function edit($id)
    {
        $timetable = Timetable::with(['class', 'teacher', 'course', 'day', 'timeSlot'])->findOrFail($id);
        return response()->json($timetable);
    }
}


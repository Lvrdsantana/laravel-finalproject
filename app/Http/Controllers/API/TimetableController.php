<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Models\Course;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $query = Timetable::with(['course', 'class', 'teacher'])
            ->orderBy('day')
            ->orderBy('start_time');

        // Filtrer par rôle
        if ($request->user()->role === 'teacher') {
            $query->where('teacher_id', $request->user()->id);
        } elseif ($request->user()->role === 'student') {
            $query->whereHas('class', function ($q) use ($request) {
                $q->where('id', $request->user()->class_id);
            });
        }

        // Filtrer par semaine si spécifié
        if ($request->has('week')) {
            $query->where('week', $request->week);
        }

        $timetables = $query->get();

        return response()->json([
            'timetables' => $timetables->map(function ($timetable) {
                return [
                    'id' => $timetable->id,
                    'day' => $timetable->day,
                    'week' => $timetable->week,
                    'start_time' => $timetable->start_time,
                    'end_time' => $timetable->end_time,
                    'course' => [
                        'id' => $timetable->course->id,
                        'name' => $timetable->course->name,
                    ],
                    'class' => [
                        'id' => $timetable->class->id,
                        'name' => $timetable->class->name,
                    ],
                    'teacher' => [
                        'id' => $timetable->teacher->id,
                        'name' => $timetable->teacher->name,
                    ],
                    'room' => $timetable->room,
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day' => 'required|integer|between:1,7',
            'week' => 'required|integer|min:1',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'course_id' => 'required|exists:courses,id',
            'class_id' => 'required|exists:class_rooms,id',
            'teacher_id' => 'required|exists:users,id',
            'room' => 'required|string|max:50',
        ]);

        // Vérifier les conflits d'horaire
        $conflicts = $this->checkTimeConflicts(
            $validated['day'],
            $validated['week'],
            $validated['start_time'],
            $validated['end_time'],
            $validated['teacher_id'],
            $validated['class_id'],
            null
        );

        if ($conflicts) {
            return response()->json([
                'message' => 'Il y a un conflit d\'horaire',
                'conflicts' => $conflicts
            ], 422);
        }

        $timetable = Timetable::create($validated);

        return response()->json([
            'message' => 'Emploi du temps créé avec succès',
            'timetable' => $timetable->load(['course', 'class', 'teacher'])
        ], 201);
    }

    public function update(Request $request, Timetable $timetable)
    {
        $validated = $request->validate([
            'day' => 'required|integer|between:1,7',
            'week' => 'required|integer|min:1',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'course_id' => 'required|exists:courses,id',
            'class_id' => 'required|exists:class_rooms,id',
            'teacher_id' => 'required|exists:users,id',
            'room' => 'required|string|max:50',
        ]);

        // Vérifier les conflits d'horaire
        $conflicts = $this->checkTimeConflicts(
            $validated['day'],
            $validated['week'],
            $validated['start_time'],
            $validated['end_time'],
            $validated['teacher_id'],
            $validated['class_id'],
            $timetable->id
        );

        if ($conflicts) {
            return response()->json([
                'message' => 'Il y a un conflit d\'horaire',
                'conflicts' => $conflicts
            ], 422);
        }

        $timetable->update($validated);

        return response()->json([
            'message' => 'Emploi du temps mis à jour avec succès',
            'timetable' => $timetable->load(['course', 'class', 'teacher'])
        ]);
    }

    public function destroy(Timetable $timetable)
    {
        $timetable->delete();

        return response()->json([
            'message' => 'Emploi du temps supprimé avec succès'
        ]);
    }

    private function checkTimeConflicts($day, $week, $start_time, $end_time, $teacher_id, $class_id, $exclude_id = null)
    {
        $query = Timetable::where('day', $day)
            ->where('week', $week)
            ->where(function ($q) use ($start_time, $end_time) {
                $q->whereBetween('start_time', [$start_time, $end_time])
                    ->orWhereBetween('end_time', [$start_time, $end_time])
                    ->orWhere(function ($q) use ($start_time, $end_time) {
                        $q->where('start_time', '<=', $start_time)
                            ->where('end_time', '>=', $end_time);
                    });
            })
            ->where(function ($q) use ($teacher_id, $class_id) {
                $q->where('teacher_id', $teacher_id)
                    ->orWhere('class_id', $class_id);
            });

        if ($exclude_id) {
            $query->where('id', '!=', $exclude_id);
        }

        return $query->get();
    }
} 
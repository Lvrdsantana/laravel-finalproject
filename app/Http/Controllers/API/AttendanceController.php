<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Models\StudentPresence;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request, Timetable $timetable)
    {
        // Vérifier que l'utilisateur a le droit d'accéder à cette présence
        if ($request->user()->role === 'teacher' && $timetable->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $students = User::where('role', 'student')
            ->whereHas('class', function ($query) use ($timetable) {
                $query->where('id', $timetable->class_id);
            })
            ->with(['presences' => function ($query) use ($timetable) {
                $query->where('timetable_id', $timetable->id);
            }])
            ->get();

        return response()->json([
            'timetable' => [
                'id' => $timetable->id,
                'date' => $timetable->date,
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
            ],
            'students' => $students->map(function ($student) {
                $presence = $student->presences->first();
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'status' => $presence ? $presence->status : null,
                    'justification' => $presence ? $presence->justification : null,
                    'justified' => $presence ? $presence->justified : false,
                ];
            }),
        ]);
    }

    public function store(Request $request, Timetable $timetable)
    {
        // Vérifier que l'enseignant est bien responsable de ce cours
        if ($request->user()->role !== 'teacher' || $timetable->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'presences' => 'required|array',
            'presences.*.student_id' => 'required|exists:users,id',
            'presences.*.status' => 'required|in:present,absent,late',
            'presences.*.justification' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['presences'] as $presence) {
                StudentPresence::updateOrCreate(
                    [
                        'timetable_id' => $timetable->id,
                        'student_id' => $presence['student_id'],
                    ],
                    [
                        'status' => $presence['status'],
                        'justification' => $presence['justification'] ?? null,
                    ]
                );
            }
            DB::commit();

            return response()->json([
                'message' => 'Présences enregistrées avec succès'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erreur lors de l\'enregistrement des présences'
            ], 500);
        }
    }

    public function justify(Request $request, StudentPresence $presence)
    {
        // Vérifier que l'utilisateur est un coordinateur
        if ($request->user()->role !== 'coordinator') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'justification' => 'required|string|max:255',
        ]);

        $presence->update([
            'justification' => $validated['justification'],
            'justified' => true,
        ]);

        return response()->json([
            'message' => 'Absence justifiée avec succès',
            'presence' => $presence
        ]);
    }

    public function studentAttendance(Request $request)
    {
        $user = $request->user();
        
        // Si c'est un étudiant, on récupère ses présences
        if ($user->role === 'student') {
            $presences = StudentPresence::where('student_id', $user->id)
                ->with(['timetable.course', 'timetable.teacher'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'presences' => $presences->map(function ($presence) {
                    return [
                        'id' => $presence->id,
                        'date' => $presence->timetable->date,
                        'status' => $presence->status,
                        'justified' => $presence->justified,
                        'justification' => $presence->justification,
                        'course' => [
                            'id' => $presence->timetable->course->id,
                            'name' => $presence->timetable->course->name,
                        ],
                        'teacher' => [
                            'id' => $presence->timetable->teacher->id,
                            'name' => $presence->timetable->teacher->name,
                        ],
                    ];
                })
            ]);
        }

        return response()->json(['message' => 'Non autorisé'], 403);
    }
} 
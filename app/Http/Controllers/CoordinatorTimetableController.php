<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use App\Models\TimetableHistory;
use App\Models\Courses;
use App\Models\Classes;
use App\Models\Teachers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Excel;

class CoordinatorTimetableController extends Controller
{
    public function index()
    {
        $courses = Courses::all();
        $classes = Classes::all();
        $teachers = Teachers::all();
        $timetables = Timetable::with(['course', 'class', 'teacher'])
            ->get()
            ->groupBy('class_id');
        
        // Récupérer l'historique global pour la vue
        $timetableHistories = TimetableHistory::with(['modifier', 'class', 'course', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->take(50) // Limiter aux 50 dernières modifications
            ->get();

        return view('backup.CoodinatorsTimetable', compact(
            'courses',
            'classes',
            'teachers',
            'timetables',
            'timetableHistories'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $timetable = Timetable::create($validated);

        return redirect()->back()->with('success', 'Emploi du temps créé avec succès');
    }

    public function history(Timetable $timetable)
    {
        $histories = $timetable->histories()
            ->with(['modifier', 'class', 'course', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('coordinator.timetable.history', compact('timetable', 'histories'));
    }

    public function filterHistory(Request $request)
    {
        $query = TimetableHistory::with(['modifier', 'class', 'course', 'teacher'])
            ->orderBy('created_at', 'desc');

        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $histories = $query->get()->map(function ($history) {
            return [
                'created_at' => $history->created_at->format('Y-m-d H:i:s'),
                'class_name' => $history->class->name,
                'course_name' => $history->course->name,
                'teacher_name' => $history->teacher->name,
                'action' => ucfirst($history->action),
                'modifier_name' => $history->modifier->name,
                'changes' => $history->changes
            ];
        });

        return response()->json($histories);
    }

    public function update(Request $request, Timetable $timetable)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $timetable->update($validated);

        return redirect()->back()->with('success', 'Emploi du temps mis à jour avec succès');
    }

    public function destroy(Timetable $timetable)
    {
        $timetable->delete();

        return redirect()->back()->with('success', 'Emploi du temps supprimé avec succès');
    }

    public function historyIndex(Request $request)
    {
        $query = TimetableHistory::with(['modifier', 'class', 'course', 'teacher', 'timetable']);

        // Filtre par classe
        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        // Filtre par type d'action
        if ($request->action) {
            $query->where('action', $request->action);
        }

        // Filtre par période
        if ($request->period) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Recherche textuelle
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('class', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('course', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('teacher', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('modifier', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Statistiques
        $todayCount = TimetableHistory::whereDate('created_at', today())->count();
        $createdCount = TimetableHistory::where('action', 'created')->count();
        $updatedCount = TimetableHistory::where('action', 'updated')->count();
        $deletedCount = TimetableHistory::where('action', 'deleted')->count();

        $histories = $query->orderBy('created_at', 'desc')->paginate(20);
        $classes = Classes::all();

        return view('coordinator.timetable.history-index', compact(
            'histories',
            'classes',
            'todayCount',
            'createdCount',
            'updatedCount',
            'deletedCount'
        ));
    }

    public function exportHistory(Request $request)
    {
        $format = $request->format ?? 'pdf';
        $query = TimetableHistory::with(['modifier', 'class', 'course', 'teacher', 'timetable']);

        // Appliquer les mêmes filtres que pour l'affichage
        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->action) {
            $query->where('action', $request->action);
        }

        $histories = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'pdf') {
            $pdf = PDF::loadView('coordinator.timetable.history-pdf', compact('histories'));
            return $pdf->download('historique-emplois-du-temps.pdf');
        } else {
            return Excel::download(new TimetableHistoryExport($histories), 'historique-emplois-du-temps.xlsx');
        }
    }
} 
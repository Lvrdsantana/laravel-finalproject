<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Classes::withCount('students')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('coordinator.classes.index', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:classes'
        ]);

        Classes::create([
            'name' => $request->name
        ]);

        return redirect()->route('classes.index')
            ->with('success', 'Classe créée avec succès');
    }

    public function update(Request $request, Classes $class)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:classes,name,' . $class->id
        ]);

        $class->update([
            'name' => $request->name
        ]);

        return redirect()->route('classes.index')
            ->with('success', 'Classe modifiée avec succès');
    }

    public function destroy(Classes $class)
    {
        if ($class->students()->count() > 0) {
            return redirect()->route('classes.index')
                ->with('error', 'Impossible de supprimer une classe qui contient des étudiants');
        }

        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Classe supprimée avec succès');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Students;
use App\Models\User;
use App\Models\classes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function dashboard()
    {
        $student = auth()->user()->student;
        if (!$student) {
            return redirect()->back()->with('error', 'Étudiant non trouvé');
        }

        // Log pour déboguer
        \Log::info('Student Dashboard Access', [
            'user_id' => auth()->id(),
            'student_id' => $student->id
        ]);

        $stats = $student->getAttendanceStats();
        
        return view('StudentDashboard', compact('student', 'stats'));
    }

    public function index()
    {
        $students = students::with('user', 'class')->get();
        return view('students.index', compact('students'));
    }

    public function create()
    {
        $classes = classes::all();
        return view('students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        students::create([
            'user_id' => $request->user_id,
            'class_id' => $request->class_id,
        ]);

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    public function show($id)
    {
        $student = students::with('user', 'class')->findOrFail($id);
        return view('students.show', compact('student'));
    }

    public function edit($id)
    {
        $student = students::findOrFail($id);
        $classes = classes::all();
        return view('students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        $student = students::findOrFail($id);
        $student->update([
            'user_id' => $request->user_id,
            'class_id' => $request->class_id,
        ]);

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy($id)
    {
        $student = students::findOrFail($id);
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $user = auth()->user();
        
        // Vérifier le mot de passe actuel si un nouveau mot de passe est fourni
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
            }
            
            $user->password = Hash::make($request->new_password);
        }

        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}

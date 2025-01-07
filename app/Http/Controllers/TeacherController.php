<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\teachers;
use App\Models\User;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = teachers::with('user')->get();
        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('teachers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        teachers::create([
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('teachers.index')->with('success', 'Teacher created successfully.');
    }

    public function show($id)
    {
        $teacher = teachers::with('user')->findOrFail($id);
        return view('teachers.show', compact('teacher'));
    }

    public function edit($id)
    {
        $teacher = teachers::findOrFail($id);
        return view('teachers.edit', compact('teacher'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $teacher = teachers::findOrFail($id);
        $teacher->update([
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function destroy($id)
    {
        $teacher = teachers::findOrFail($id);
        $teacher->delete();

        return redirect()->route('teachers.index')->with('success', 'Teacher deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class TeacherProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $timetables = $user->teacherTimetables()
            ->with(['course', 'class'])
            ->get();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
                'created_at' => $user->created_at->format('Y-m-d'),
            ],
            'stats' => [
                'total_hours' => $timetables->count(),
                'total_classes' => $timetables->groupBy('class_id')->count(),
                'total_courses' => $timetables->groupBy('course_id')->count(),
            ],
            'courses' => $timetables->groupBy('course_id')->map(function ($lessons) {
                $firstLesson = $lessons->first();
                return [
                    'id' => $firstLesson->course->id,
                    'name' => $firstLesson->course->name,
                    'lessons_count' => $lessons->count(),
                ];
            })->values(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'required_with:current_password', Password::min(8)],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('new_password')) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            ]
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $validated = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048']
        ]);

        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'message' => 'Avatar mis à jour avec succès',
            'avatar_url' => asset('storage/' . $path)
        ]);
    }
} 
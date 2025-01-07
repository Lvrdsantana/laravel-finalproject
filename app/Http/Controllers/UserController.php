<?php

namespace App\Http\Controllers;

use App\Models\classes;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ParentModel; 
use App\Models\coordinators;
use App\Models\courses;
use App\Models\Students;
use App\Models\teachers;
use App\Models\Timetable;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('dashboard', compact('users'));
    }

    public function search(Request $request)
    {
        $search = $request->get('search');
        
        $users = User::where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('role', 'like', "%{$search}%")
            ->orWhere('id', 'like', "%{$search}%")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if($request->ajax()) {
            return response()->json([
                'users' => $users,
                'pagination' => $users->links()->toHtml()
            ]);
        }

        return view('dashboard', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string',
            'class_id' => 'required_if:role,students|integer|nullable',
            'student_ids' => 'required_if:role,parents|array|nullable',
            'student_ids.*' => 'exists:students,id'
        ]);

        // Générer un mot de passe aléatoire
        $randomPassword = Str::random(10); 
        
        // Créer l'utilisateur
        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
            'class_id' => $request->role === 'students' ? $request->class_id : null,
            'password' => bcrypt($randomPassword),
        ]);

        // Gérer l'insertion dans les tables correspondantes en fonction du rôle
        switch ($request->role) {
            case 'students':
                Students::create([
                    'user_id' => $user->id,
                    'class_id' => $request->class_id,
                ]);
                break;
            case 'teachers':
                teachers::create(['user_id' => $user->id]);
                break;
            case 'parents':
                $parent = ParentModel::create(['user_id' => $user->id]);
                if ($request->has('student_ids')) {
                    $parent->students()->attach($request->student_ids);
                }
                break;
            case 'coordinators':
                coordinators::create(['user_id' => $user->id]);
                break;
        }

        // Stocker le mot de passe en session
        $request->session()->put('generated_password', $randomPassword);
        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('edit-user', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Valider les données de la requête
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'class_id' => 'required_if:role,students|integer|nullable',
            'student_ids' => 'required_if:role,parents|array|nullable',
            'student_ids.*' => 'exists:students,id'
        ]);
    
        // Sauvegarder l'ancien rôle de l'utilisateur
        $oldRole = $user->role;
    
        // Mettre à jour les informations de base de l'utilisateur
        $user->update([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
        ]);
    
        // Supprimer les données associées à l'ancien rôle
        switch ($oldRole) {
            case 'students':
                Students::where('user_id', $user->id)->delete();
                break;
            case 'teachers':
                teachers::where('user_id', $user->id)->delete();
                break;
            case 'parents':
                if ($parent = ParentModel::where('user_id', $user->id)->first()) {
                    $parent->students()->detach();
                    $parent->delete();
                }
                break;
            case 'coordinators':
                coordinators::where('user_id', $user->id)->delete();
                break;
        }
    
        // Mettre à jour la table correspondante en fonction du nouveau rôle
        switch ($request->role) {
            case 'students':
                Students::updateOrCreate(
                    ['user_id' => $user->id],
                    ['class_id' => $request->class_id]
                );
                break;
            case 'teachers':
                teachers::updateOrCreate(['user_id' => $user->id]);
                break;
            case 'parents':
                $parent = ParentModel::updateOrCreate(['user_id' => $user->id]);
                if ($request->has('student_ids')) {
                    $parent->students()->sync($request->student_ids);
                }
                break;
            case 'coordinators':
                coordinators::updateOrCreate(['user_id' => $user->id]);
                break;
        }
    
        // Rediriger avec un message de succès
        return redirect()->route('dashboard')->with('success', 'User updated successfully.');
    }    
    public function destroy(User $user)
    {
        // Supprimer les informations associées en fonction du rôle
        switch ($user->role) {
            case 'students':
                Students::where('user_id', $user->id)->delete();
                break;
            case 'teachers':
                teachers::where('user_id', $user->id)->delete();
                break;
            case 'parents':
                ParentModel::where('user_id', $user->id)->delete();
                break;
            case 'coordinators':
                coordinators::where('user_id', $user->id)->delete();
                break;
        }

        $user->delete();
        return redirect()->route('dashboard')->with('success', 'User deleted successfully.');
    }
    public function showTimetable() {
        // Récupérer toutes les classes
        $classes = classes::all(); 
        // Récupérer tous les cours
        $courses = courses::all(); 
        // Récupérer tous les enseignants
        $teachers = teachers::all(); // Assurez-vous que le modèle 'teachers' existe et est importé
    
        $timetables = Timetable::all(); // Récupérer les données
        
        // Passer les variables à la vue
        return view('CoodinatorsTimetable', compact('classes', 'courses', 'teachers','timetables'));
    }

    public function storeTimetable(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'day' => 'required|string',
            'time' => 'required|string',
        ]);
    
        // Créer l'emploi du temps
        Timetable::create([
            'class_id' => $request->class_id,
            'course_id' => $request->course_id,
            'teacher_id' => $request->teacher_id,
            'day' => $request->day,
            'time' => $request->time,
        ]);
    
        return redirect()->back()->with('success', 'Timetable entry created successfully.');
    }
    
}

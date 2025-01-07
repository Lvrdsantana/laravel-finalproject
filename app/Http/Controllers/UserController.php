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

/**
 * Contrôleur pour la gestion des utilisateurs
 * 
 * Ce contrôleur gère toutes les opérations CRUD liées aux utilisateurs :
 * - Liste et recherche des utilisateurs
 * - Création d'un nouvel utilisateur (avec gestion des rôles)
 * - Modification d'un utilisateur existant
 * - Suppression d'un utilisateur
 * - Gestion des emplois du temps
 */
class UserController extends Controller
{
    /**
     * Affiche la liste paginée des utilisateurs
     * 
     * @return \Illuminate\View\View Vue avec la liste des utilisateurs
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('dashboard', compact('users'));
    }

    /**
     * Recherche des utilisateurs selon différents critères
     * 
     * @param Request $request Requête contenant les critères de recherche
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse Vue ou réponse JSON selon le type de requête
     */
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

    /**
     * Crée un nouvel utilisateur avec son rôle spécifique
     * 
     * @param Request $request Données du formulaire de création
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès
     */
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
        
        // Créer l'utilisateur de base
        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
            'class_id' => $request->role === 'students' ? $request->class_id : null,
            'password' => bcrypt($randomPassword),
        ]);

        // Créer l'entrée correspondante selon le rôle
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

        // Stocker temporairement le mot de passe généré
        $request->session()->put('generated_password', $randomPassword);
        return redirect()->back()->with('success', 'User created successfully.');
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur
     * 
     * @param User $user Utilisateur à modifier
     * @return \Illuminate\View\View Vue du formulaire d'édition
     */
    public function edit(User $user)
    {
        return view('edit-user', compact('user'));
    }

    /**
     * Met à jour les informations d'un utilisateur
     * 
     * @param Request $request Données du formulaire
     * @param User $user Utilisateur à modifier
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès
     */
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
    
        // Sauvegarder l'ancien rôle pour la gestion des données associées
        $oldRole = $user->role;
    
        // Mettre à jour les informations de base
        $user->update([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
        ]);
    
        // Nettoyer les anciennes données de rôle
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
    
        // Créer les nouvelles données selon le rôle
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
    
        return redirect()->route('dashboard')->with('success', 'User updated successfully.');
    }    

    /**
     * Supprime un utilisateur et ses données associées
     * 
     * @param User $user Utilisateur à supprimer
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès
     */
    public function destroy(User $user)
    {
        // Supprimer les données spécifiques au rôle
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

    /**
     * Affiche l'emploi du temps avec toutes les données nécessaires
     * 
     * @return \Illuminate\View\View Vue de l'emploi du temps
     */
    public function showTimetable() {
        // Récupérer les données nécessaires
        $classes = classes::all(); 
        $courses = courses::all(); 
        $teachers = teachers::all();
        $timetables = Timetable::all();
        
        return view('CoodinatorsTimetable', compact('classes', 'courses', 'teachers','timetables'));
    }

    /**
     * Crée une nouvelle entrée dans l'emploi du temps
     * 
     * @param Request $request Données du formulaire
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès
     */
    public function storeTimetable(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'day' => 'required|string',
            'time' => 'required|string',
        ]);
    
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

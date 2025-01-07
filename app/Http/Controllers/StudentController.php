<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Students;
use App\Models\User;
use App\Models\classes; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Contrôleur pour la gestion des étudiants
 * 
 * Ce contrôleur gère toutes les opérations liées aux étudiants :
 * - Tableau de bord étudiant
 * - CRUD (Create, Read, Update, Delete) des étudiants
 * - Mise à jour du profil étudiant
 */
class StudentController extends Controller
{
    /**
     * Affiche le tableau de bord de l'étudiant connecté
     * 
     * @return \Illuminate\View\View Vue du tableau de bord avec les statistiques
     */
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

    /**
     * Affiche la liste de tous les étudiants
     * 
     * @return \Illuminate\View\View Vue avec la liste des étudiants
     */
    public function index()
    {
        $students = students::with('user', 'class')->get();
        return view('students.index', compact('students'));
    }

    /**
     * Affiche le formulaire de création d'un étudiant
     * 
     * @return \Illuminate\View\View Vue du formulaire de création
     */
    public function create()
    {
        $classes = classes::all();
        return view('students.create', compact('classes'));
    }

    /**
     * Enregistre un nouvel étudiant dans la base de données
     * 
     * @param Request $request Les données du formulaire
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès
     */
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

    /**
     * Affiche les détails d'un étudiant spécifique
     * 
     * @param int $id L'identifiant de l'étudiant
     * @return \Illuminate\View\View Vue avec les détails de l'étudiant
     */
    public function show($id)
    {
        $student = students::with('user', 'class')->findOrFail($id);
        return view('students.show', compact('student'));
    }

    /**
     * Affiche le formulaire d'édition d'un étudiant
     * 
     * @param int $id L'identifiant de l'étudiant à modifier
     * @return \Illuminate\View\View Vue du formulaire d'édition
     */
    public function edit($id)
    {
        $student = students::findOrFail($id);
        $classes = classes::all();
        return view('students.edit', compact('student', 'classes'));
    }

    /**
     * Met à jour les informations d'un étudiant
     * 
     * @param Request $request Les nouvelles données
     * @param int $id L'identifiant de l'étudiant
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès
     */
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

    /**
     * Supprime un étudiant de la base de données
     * 
     * @param int $id L'identifiant de l'étudiant à supprimer
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès
     */
    public function destroy($id)
    {
        $student = students::findOrFail($id);
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    /**
     * Met à jour le profil de l'étudiant connecté
     * 
     * @param Request $request Les données du formulaire
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès/erreur
     */
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

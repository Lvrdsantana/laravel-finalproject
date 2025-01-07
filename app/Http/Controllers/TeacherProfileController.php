<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;

/** inachevé
 * Contrôleur pour gérer le profil de l'enseignant
 * 
 * Ce contrôleur permet de :
 * - Mettre à jour les informations du profil
 * - Mettre à jour l'avatar du profil
 */
class TeacherProfileController extends Controller
{
    /**
     * Mettre à jour les informations du profil
     * 
     * @param Request $request Les données du formulaire
     * @return \Illuminate\Http\RedirectResponse Redirection vers la page de profil
     */
    public function update(Request $request)
    {
        try {
            $user = auth()->user();
            
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'current_password' => ['required_with:new_password', 'current_password'],
                'new_password' => ['nullable', 'required_with:current_password', Password::min(8), 'confirmed'],
            ], [
                'name.required' => 'Le nom est requis.',
                'email.required' => 'L\'email est requis.',
                'email.email' => 'L\'email doit être une adresse email valide.',
                'email.unique' => 'Cet email est déjà utilisé.',
                'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
                'new_password.confirmed' => 'La confirmation du nouveau mot de passe ne correspond pas.',
                'new_password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            ]);

            $user->name = $validated['name'];
            $user->email = $validated['email'];

            if ($request->filled('new_password')) {
                $user->password = Hash::make($validated['new_password']);
            }

            $user->save();

            return redirect()->back()->with('success', 'Profil mis à jour avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du profil : ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour du profil.']);
        }
    }

    /**
     * Mettre à jour l'avatar du profil
     * 
     * @param Request $request Les données du formulaire
     * @return \Illuminate\Http\RedirectResponse Redirection vers la page de profil
     */
    public function updateAvatar(Request $request)
    {
        try {
            $validated = $request->validate([
                'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'] // 2MB max
            ], [
                'avatar.required' => 'Veuillez sélectionner une image.',
                'avatar.image' => 'Le fichier doit être une image.',
                'avatar.mimes' => 'L\'image doit être au format JPEG, PNG ou JPG.',
                'avatar.max' => 'L\'image ne doit pas dépasser 2MB.',
            ]);

            $user = auth()->user();

            // Supprimer l'ancien avatar s'il existe
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Stocker le nouvel avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();

            return redirect()->back()->with('success', 'Photo de profil mise à jour avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'avatar : ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['avatar' => 'Une erreur est survenue lors de la mise à jour de la photo de profil.']);
        }
    }
} 
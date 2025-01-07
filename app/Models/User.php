<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle User - Gère les utilisateurs du système
 * 
 * Ce modèle représente un utilisateur dans le système avec authentification.
 * Il peut être associé à un enseignant ou un étudiant via des relations.
 */
class User extends Authenticatable
{
    use HasFactory;

    /**
     * Retourne le nom de la colonne d'identifiant pour l'authentification
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Retourne l'identifiant de l'utilisateur pour l'authentification
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     * Retourne le mot de passe hashé de l'utilisateur
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Retourne le nom du champ mot de passe dans la base de données
     */
    public function getAuthPasswordName()
    {
        return 'password';
    }

    /**
     * Récupère le token "Se souvenir de moi"
     */
    public function getRememberToken()
    {
        if (! empty($this->getRememberTokenName())) {
            return $this->{$this->getRememberTokenName()};
        }
    }

    /**
     * Définit le token "Se souvenir de moi"
     */
    public function setRememberToken($value)
    {
        if (! empty($this->getRememberTokenName())) {
            $this->{$this->getRememberTokenName()} = $value;
        }
    }

    /**
     * Retourne le nom du champ pour le token "Se souvenir de moi"
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    use Notifiable;

    /**
     * Les attributs qui peuvent être assignés en masse
     *
     * @var array
     */
    protected $fillable = [
        'name',     // Nom de l'utilisateur
        'email',    // Email de l'utilisateur
        'password', // Mot de passe hashé
        'role',     // Rôle de l'utilisateur (student, teacher, etc.)
    ];

    /**
     * Les attributs qui doivent être cachés dans les tableaux
     *
     * @var array
     */
    protected $hidden = [
        'password',       // Cache le mot de passe
        'remember_token', // Cache le token "Se souvenir de moi"
    ];

    /**
     * Relation avec le modèle Teachers
     * Un utilisateur peut être un enseignant
     */
    public function teacher()
    {
        return $this->hasOne(Teachers::class);
    }

    /**
     * Relation avec le modèle Students
     * Un utilisateur peut être un étudiant
     */
    public function student()
    {
        return $this->hasOne(Students::class, 'user_id');
    }
}
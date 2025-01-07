<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Teachers - Gère les enseignants
 * 
 * Ce modèle représente un enseignant dans le système,
 * associé à un utilisateur et pouvant marquer les présences.
 */
class Teachers extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée dans la base de données
     */
    protected $table = 'teachers';

    /**
     * Liste des champs qui peuvent être remplis en masse
     * 
     * @var array
     */
    protected $fillable = [
        'user_id', // ID de l'utilisateur associé
    ];

    /**
     * Relation avec le modèle User
     * Récupère l'utilisateur associé à cet enseignant
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le modèle Attendance
     * Récupère toutes les présences marquées par cet enseignant
     */
    public function markedAttendances()
    {
        return $this->hasMany(Attendance::class, 'marked_by');
    }
}

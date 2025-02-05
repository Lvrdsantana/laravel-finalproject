<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Parent - Gère les parents des étudiants
 * 
 * Ce modèle représente un parent d'un étudiant,
 * associé à un utilisateur.
 */
class ParentModel extends Model
{
    use HasFactory;

    protected $table = 'parents'; //table dans la base de données

    protected $fillable = [
        'user_id',
    ];

    // Relation avec le modèle User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->belongsToMany(Students::class, 'parent_student', 'parent_id', 'student_id')
                    ->withTimestamps();
    }
}

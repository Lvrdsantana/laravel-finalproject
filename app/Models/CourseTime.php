<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Classe;

/**
 * Modèle CourseTime - Gère les créneaux horaires des cours
 * 
 * Ce modèle représente un créneau horaire spécifique pour un cours,
 * associé à une classe et un enseignant.
 */
class CourseTime extends Model
{
    /**
     * Nom de la table associée dans la base de données
     */
    protected $table = 'course_time';

    /**
     * Liste des champs qui peuvent être remplis en masse
     * 
     * @var array
     */
    protected $fillable = [
        'class_id',    // ID de la classe
        'course_id',   // ID du cours
        'teacher_id',  // ID de l'enseignant
        'date',        // Date du cours
        'start_time',  // Heure de début
        'end_time',    // Heure de fin
    ];

    /**
     * Relation avec le modèle classes
     * Récupère la classe associée à ce créneau horaire
     */
    public function class()
    {
        return $this->belongsTo(classes::class);
    }

    /**
     * Relation avec le modèle courses
     * Récupère le cours associé à ce créneau horaire
     */
    public function course()
    {
        return $this->belongsTo(courses::class);
    }

    /**
     * Relation avec le modèle teachers
     * Récupère l'enseignant associé à ce créneau horaire
     */
    public function teacher()
    {
        return $this->belongsTo(teachers::class);
    }
}
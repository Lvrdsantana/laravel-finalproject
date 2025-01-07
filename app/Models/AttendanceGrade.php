<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle pour gérer les notes de présence des étudiants
 * 
 * Ce modèle stocke les statistiques de présence d'un étudiant pour un cours donné :
 * - Note globale de présence
 * - Nombre total de séances
 * - Nombre de séances suivies
 * - Semestre et année académique
 */
class AttendanceGrade extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse
     * 
     * @var array
     */
    protected $fillable = [
        'student_id',     // ID de l'étudiant
        'course_id',      // ID du cours
        'grade',          // Note de présence calculée
        'total_sessions', // Nombre total de séances du cours
        'attended_sessions', // Nombre de séances auxquelles l'étudiant a assisté
        'semester',       // Semestre concerné
        'academic_year'   // Année académique
    ];

    /**
     * Relation avec le modèle Students
     * Une note de présence appartient à un étudiant
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Students::class);
    }

    /**
     * Relation avec le modèle courses
     * Une note de présence est liée à un cours spécifique
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(courses::class);
    }
} 
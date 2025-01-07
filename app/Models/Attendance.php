<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle pour gérer les présences des étudiants
 * 
 * Ce modèle stocke les informations de présence pour chaque étudiant à chaque cours :
 * - Le cours concerné (timetable)
 * - L'étudiant
 * - Le statut de présence
 * - L'enseignant qui a fait l'appel
 * - La date et l'heure de l'appel
 */
class Attendance extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse
     * 
     * @var array
     */
    protected $fillable = [
        'timetable_id',  // ID du cours
        'student_id',    // ID de l'étudiant
        'status',        // Statut de présence (présent, absent, etc.)
        'marked_by',     // ID de l'enseignant qui a fait l'appel
        'marked_at',     // Date et heure de l'appel
        'date'          // Date du cours
    ];

    /**
     * Les attributs à convertir automatiquement
     * 
     * @var array
     */
    protected $casts = [
        'marked_at' => 'datetime', // Conversion en objet DateTime
        'date' => 'date'          // Conversion en objet Date
    ];

    /**
     * Relation avec le modèle Justification
     * Une présence peut avoir une justification d'absence
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function justification()
    {
        return $this->hasOne(Justification::class);
    }

    /**
     * Vérifie si l'absence est justifiée
     * 
     * @return bool True si une justification existe
     */
    public function isJustified()
    {
        return $this->justification()->exists();
    }

    /**
     * Relation avec le modèle Timetable
     * Une présence est liée à un cours spécifique
     * withTrashed() permet d'accéder aux cours supprimés
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function timetable()
    {
        return $this->belongsTo(Timetable::class)->withTrashed();
    }

    /**
     * Relation avec le modèle Students
     * Une présence concerne un étudiant spécifique
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    /**
     * Relation avec le modèle Teachers
     * Une présence est marquée par un enseignant
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teacher()
    {
        return $this->belongsTo(Teachers::class, 'marked_by');
    }
} 
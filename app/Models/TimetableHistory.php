<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle TimetableHistory - Gère l'historique des modifications d'emploi du temps
 * 
 * Ce modèle enregistre toutes les modifications apportées aux emplois du temps,
 * permettant de suivre qui a fait quels changements et quand.
 */
class TimetableHistory extends Model
{
    /**
     * Liste des champs qui peuvent être remplis en masse
     * 
     * @var array
     */
    protected $fillable = [
        'timetable_id',  // ID de l'emploi du temps modifié
        'class_id',      // ID de la classe concernée
        'course_id',     // ID du cours concerné
        'teacher_id',    // ID de l'enseignant concerné
        'day_id',        // ID du jour concerné
        'time_slot_id',  // ID du créneau horaire
        'color',         // Couleur d'affichage
        'action',        // Type d'action effectuée (création, modification, suppression)
        'modified_by',   // ID de l'utilisateur ayant effectué la modification
        'changes'        // Tableau JSON des modifications effectuées
    ];

    /**
     * Définition des conversions de type pour certains attributs
     * 
     * @var array
     */
    protected $casts = [
        'changes' => 'array' // Conversion automatique du JSON en tableau
    ];

    /**
     * Relation avec le modèle Timetable
     * Une entrée d'historique appartient à un emploi du temps
     */
    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    /**
     * Relation avec le modèle Classes
     * Une entrée d'historique est liée à une classe
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Relation avec le modèle Courses
     * Une entrée d'historique est liée à un cours
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Courses::class, 'course_id');
    }

    /**
     * Relation avec le modèle Teachers
     * Une entrée d'historique est liée à un enseignant
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teachers::class, 'teacher_id');
    }

    /**
     * Relation avec le modèle Days
     * Une entrée d'historique est liée à un jour
     */
    public function day(): BelongsTo
    {
        return $this->belongsTo(Days::class, 'day_id');
    }

    /**
     * Relation avec le modèle TimeSlots
     * Une entrée d'historique est liée à un créneau horaire
     */
    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlots::class, 'time_slot_id');
    }

    /**
     * Relation avec le modèle User
     * Une entrée d'historique est liée à l'utilisateur qui a effectué la modification
     */
    public function modifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}

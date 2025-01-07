<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modèle Timetable - Gère les emplois du temps
 * 
 * Ce modèle représente un créneau d'emploi du temps, associant :
 * - Une classe
 * - Un enseignant 
 * - Un jour
 * - Un créneau horaire
 * - Un cours
 * 
 * Il gère également l'historique des modifications via le trait SoftDeletes
 * et des événements de modèle.
 */
class Timetable extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Liste des champs qui peuvent être remplis en masse
     */
    protected $fillable = [
        'class_id',     // ID de la classe
        'teacher_id',   // ID de l'enseignant
        'day_id',       // ID du jour
        'time_slot_id', // ID du créneau horaire
        'color',        // Couleur d'affichage
        'course_id'     // ID du cours
    ];

    /**
     * Relation avec le modèle Classes
     * Un emploi du temps appartient à une classe
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Relation avec le modèle Teachers
     * Un emploi du temps est associé à un enseignant
     */
    public function teacher()
    {
        return $this->belongsTo(Teachers::class, 'teacher_id');
    }

    /**
     * Relation avec le modèle Days
     * Un emploi du temps est associé à un jour
     */
    public function day()
    {
        return $this->belongsTo(Days::class, 'day_id');
    }

    /**
     * Relation avec le modèle TimeSlots
     * Un emploi du temps est associé à un créneau horaire
     */
    public function timeSlot()
    {
        return $this->belongsTo(TimeSlots::class, 'time_slot_id');
    }

    /**
     * Relation avec le modèle Courses
     * Un emploi du temps est associé à un cours
     */
    public function course()
    {
        return $this->belongsTo(Courses::class, 'course_id');
    }

    /**
     * Relation avec le modèle Attendance
     * Un emploi du temps peut avoir plusieurs présences
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Relation avec le modèle TimetableHistory
     * Un emploi du temps a un historique de modifications
     */
    public function histories(): HasMany
    {
        return $this->hasMany(TimetableHistory::class);
    }

    /**
     * Méthode boot pour enregistrer les événements du modèle
     * Gère la création, mise à jour et suppression dans l'historique
     */
    protected static function boot()
    {
        parent::boot();

        // Lors de la création d'un emploi du temps
        static::created(function ($timetable) {
            $timetable->recordHistory('created');
        });

        // Lors de la mise à jour d'un emploi du temps
        static::updated(function ($timetable) {
            $changes = $timetable->getChanges();
            unset($changes['updated_at']);
            
            if (!empty($changes)) {
                $timetable->recordHistory('updated', $changes);
            }
        });

        // Lors de la suppression d'un emploi du temps
        static::deleted(function ($timetable) {
            $timetable->recordHistory('deleted');
        });
    }

    /**
     * Enregistre une entrée dans l'historique des modifications
     * 
     * @param string $action Type d'action (created/updated/deleted)
     * @param array|null $changes Changements effectués lors d'une mise à jour
     */
    protected function recordHistory(string $action, array $changes = null)
    {
        TimetableHistory::create([
            'timetable_id' => $this->id,
            'class_id' => $this->class_id,
            'course_id' => $this->course_id,
            'teacher_id' => $this->teacher_id,
            'day_id' => $this->day_id,
            'time_slot_id' => $this->time_slot_id,
            'color' => $this->color,
            'action' => $action,
            'modified_by' => Auth::id(),
            'changes' => $changes,
        ]);
    }
}

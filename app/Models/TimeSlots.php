<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle TimeSlots - Gère les créneaux horaires
 * 
 * Ce modèle représente un créneau horaire générique qui peut être 
 * utilisé dans les emplois du temps. Il définit une plage horaire
 * avec une heure de début et une heure de fin.
 */
class TimeSlots extends Model
{
    /**
     * Liste des champs qui peuvent être remplis en masse
     * 
     * @var array
     */
    protected $fillable = [
        'start_time', // Heure de début du créneau
        'end_time'    // Heure de fin du créneau
    ];

    /**
     * Relation avec le modèle Timetable
     * Un créneau horaire peut être utilisé dans plusieurs emplois du temps
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'time_slot_id');
    }
}
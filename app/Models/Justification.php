<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Justification - Gère les justifications d'absences
 * 
 * Ce modèle représente une justification d'absence pour un étudiant,
 * associée à une présence et un enseignant.
 */
class Justification extends Model
{
    protected $fillable = [
        'attendance_id',
        'reason',
        'justified_by',
        'justified_at'
    ];

    protected $casts = [
        'justified_at' => 'datetime'
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function justifier()
    {
        return $this->belongsTo(User::class, 'justified_by');
    }
} 
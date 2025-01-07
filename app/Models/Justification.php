<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
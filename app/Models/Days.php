<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Days extends Model
{
    // Vous pouvez spécifier la table si elle ne suit pas la convention de nommage de Laravel
    protected $table = 'days';

    // Définissez les attributs qui sont mass assignable
    protected $fillable = ['name'];

     // Relation avec Timetable
     public function timetables()
     {
         return $this->hasMany(Timetable::class, 'day_id');
        }
}
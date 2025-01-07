<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class courses extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'class_id',
        'other_columns'
    ];
    
    protected $table = 'courses';

    public function teacher()
    {
        return $this->belongsTo(Teachers::class, 'teacher_id')->with('user');
    }
}
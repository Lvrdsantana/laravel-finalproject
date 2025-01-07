<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Classe;


class course_time extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'class_id',
        'teacher_id',
        'date',
        'start_time',
        'end_time',
    ];

    public function course()
    {
        return $this->belongsTo(courses::class);
    }

    public function classe()
    {
        return $this->belongsTo(classes::class);
    }

    public function teacher()
    {
        return $this->belongsTo(teachers::class);
    }
}
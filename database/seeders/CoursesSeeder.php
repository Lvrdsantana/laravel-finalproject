<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Courses;

class CoursesSeeder extends Seeder
{
    public function run()
    {
        Courses::create(['name' => 'Mathématiques']);
        Courses::create(['name' => 'Physique']);
        Courses::create(['name' => 'Anglais']);
        // Ajoutez d'autres cours selon vos besoins
    }
} 
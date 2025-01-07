<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceGradesTable extends Migration
{
    public function up()
    {
        Schema::create('attendance_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->decimal('grade', 4, 2); // Note sur 20
            $table->integer('total_sessions');
            $table->integer('attended_sessions');
            $table->string('semester')->nullable();
            $table->string('academic_year')->nullable();
            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index(['student_id', 'course_id', 'semester']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_grades');
    }
}
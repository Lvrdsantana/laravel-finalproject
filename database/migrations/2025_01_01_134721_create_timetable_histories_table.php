<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimetableHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('timetable_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timetable_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes');
            $table->foreignId('course_id')->constrained('courses');
            $table->foreignId('teacher_id')->constrained('teachers');
            $table->foreignId('day_id')->constrained('days');
            $table->foreignId('time_slot_id')->constrained('time_slots');
            $table->string('color')->nullable();
            $table->string('action')->comment('created, updated, deleted');
            $table->foreignId('modified_by')->constrained('users');
            $table->json('changes')->nullable()->comment('Stores the changes made');
            $table->timestamps();
        });
    }
}
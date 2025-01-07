<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timetable_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('status', ['present', 'late', 'absent']);
            $table->foreignId('marked_by')->constrained('teachers');
            $table->timestamp('marked_at');
            $table->timestamps();
            $table->index(['timetable_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}; 
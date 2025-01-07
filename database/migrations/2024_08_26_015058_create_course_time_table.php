<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_time', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('course_id')->index('course_id');
            $table->integer('class_id')->index('class_id');
            $table->integer('teacher_id')->index('teacher_id');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();

             // Foreign keys
             $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
             $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
             $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_time');
    }
};

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
        Schema::table('course_time', function (Blueprint $table) {
            $table->foreign(['course_id'], 'course_time_ibfk_1')->references(['id'])->on('courses')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['class_id'], 'course_time_ibfk_2')->references(['id'])->on('classes')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['teacher_id'], 'course_time_ibfk_3')->references(['id'])->on('teachers')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_time', function (Blueprint $table) {
            $table->dropForeign('course_time_ibfk_1');
            $table->dropForeign('course_time_ibfk_2');
            $table->dropForeign('course_time_ibfk_3');
        });
    }
};

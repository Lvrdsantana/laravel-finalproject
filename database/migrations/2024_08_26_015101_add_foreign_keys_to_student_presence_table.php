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
        Schema::table('student_presence', function (Blueprint $table) {
            $table->foreign(['student_id'], 'student_presence_ibfk_1')->references(['id'])->on('students')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['course_time_id'], 'student_presence_ibfk_2')->references(['id'])->on('course_time')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_presence', function (Blueprint $table) {
            $table->dropForeign('student_presence_ibfk_1');
            $table->dropForeign('student_presence_ibfk_2');
        });
    }
};

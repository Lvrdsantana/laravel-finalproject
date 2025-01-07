<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
     

        // Créer une nouvelle table pour les justifications
        Schema::create('justifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained()->onDelete('cascade');
            $table->text('reason');
            $table->foreignId('justified_by')->constrained('users');
            $table->timestamp('justified_at');
            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index('attendance_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('justifications');
    }
}; 
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('justified')->default(false);
            $table->text('justification_reason')->nullable();
            $table->timestamp('justified_at')->nullable();
            $table->unsignedBigInteger('justified_by')->nullable();
            $table->foreign('justified_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['justified', 'justification_reason', 'justified_at', 'justified_by']);
        });
    }
}; 
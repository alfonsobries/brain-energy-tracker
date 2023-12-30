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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->json('mood');
            $table->json('sleep_quality');
            $table->json('wake_up_state');
            $table->json('symptoms');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};

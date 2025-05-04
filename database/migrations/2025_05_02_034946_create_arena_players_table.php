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
        Schema::create('arena_players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('arena_game_id')->constrained()->onDelete('cascade');
            $table->integer('score')->default(0);  // Puntaje del jugador
            $table->integer('current_question')->default(1);  // Pregunta actual que está respondiendo
            $table->foreignId('last_answered_question_id')->nullable()->constrained('quiz_questions');  // Última pregunta respondida
            $table->boolean('has_responded')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arena_players');
    }
};

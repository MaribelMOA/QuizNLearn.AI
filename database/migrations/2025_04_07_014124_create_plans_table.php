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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->integer('duration_months');
            $table->integer('summaries_allowed');
            $table->integer('quizzes_allowed');
            $table->integer('max_questions');
            $table->integer('study_mode_uses');
            $table->integer('arena_mode_uses');
            $table->integer('max_arena_players');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

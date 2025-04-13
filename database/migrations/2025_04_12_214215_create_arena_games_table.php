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
        Schema::create('arena_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_history_id')->constrained('game_histories')->onDelete('cascade');
            $table->string('pin', 8);
            $table->timestamp('start_time')->useCurrent();
            $table->timestamp('end_time')->nullable();
            $table->enum('status', ['active', 'finished'])->default('active');
            $table->integer('players_connected')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arena_games');
    }
};

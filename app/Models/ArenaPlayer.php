<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArenaPlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'arena_game_id',
        'user_id',
        'score',
        'current_question',
        'last_selected_answer_id',
        'last_answered_question_id',
        'has_responded',
        'is_host',
    ];
    protected $visible = ['id', 'name', 'arena_game_id', 'score', 'current_question'];


    public function arenaGame()
    {
        return $this->belongsTo(ArenaGame::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // En el modelo ArenaPlayer
    /**
     * Relación con las respuestas seleccionadas por el jugador
     */
    public function lastSelectedAnswer()
    {
        return $this->belongsTo(QuizAnswer::class, 'last_selected_answer_id');
    }

    /**
     * Relación con la última pregunta respondida por el jugador
     */
    public function lastAnsweredQuestion()
    {
        return $this->belongsTo(QuizQuestion::class, 'last_answered_question_id');
    }

    /**
     * Método para determinar si el jugador ha respondido a la pregunta actual
     */
    public function hasRespondedToCurrentQuestion()
    {
        return $this->current_question == $this->lastAnsweredQuestion->id && $this->has_responded;
    }

    public function selectedAnswer()
    {
        return $this->belongsTo(QuizAnswer::class, 'last_selected_answer_id');
    }



}

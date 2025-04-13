<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\QuizAnswerFactory> */
    use HasFactory;
    protected $fillable = ['question_id', 'answer_text', 'is_correct', 'explanation'];

    /**
     * Relación con la pregunta a la que pertenece la respuesta.
     */
    public function quizQuestion()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }


}

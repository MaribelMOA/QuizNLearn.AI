<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    /** @use HasFactory<\Database\Factories\QuizQuestionFactory> */
    use HasFactory;
    protected $fillable = ['quiz_id', 'question_text'];

    /**
     * Relación con el quiz al que pertenece la pregunta.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function quizQuestionAnswers(){
        return $this->hasMany(QuizAnswer::class);
    }

    // app/Models/QuizQuestion.php
    public function type()
    {
        return $this->belongsTo(QuestionType::class, 'question_type_id');
    }

}

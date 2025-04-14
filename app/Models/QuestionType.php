<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;



class QuestionType extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionTypeFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'quiz_question_type');
    }
    // app/Models/QuestionType.php
    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

}

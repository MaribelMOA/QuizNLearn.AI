<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    /** @use HasFactory<\Database\Factories\QuizFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'num_questions',
        'difficulty_level',
        'mode',
        'quiz_data',
    ];

    protected $casts = [
        'quiz_data' => 'array', // Convierte quiz_data en un array automáticamente.
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    /**
     * Obtener los resultados del cuestionario.
     */

}

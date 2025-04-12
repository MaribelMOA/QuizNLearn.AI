<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{

    use HasFactory;
    protected $fillable = [
        'name', 'price', 'description', 'duration_months',
        'summaries_allowed', 'quizzes_allowed', 'max_questions',
        'study_mode_uses', 'arena_mode_uses','max_arena_players',
        'pdf_files','urls','text_limit','questions_limit',
    ];

    public $timestamps = true;


}

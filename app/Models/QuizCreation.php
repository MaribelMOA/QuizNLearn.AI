<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizCreation extends Model
{
    protected $table = 'quiz_creations';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'created_at',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArenaResult extends Model
{
    /** @use HasFactory<\Database\Factories\ArenaResultFactory> */
    use HasFactory;


    protected $fillable = [
        'arena_game_id',
        'user_id',
        'score',
        'rank',
    ];

    public function arenaGame()
    {
        return $this->belongsTo(ArenaGame::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

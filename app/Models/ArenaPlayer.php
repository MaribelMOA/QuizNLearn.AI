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
    ];
    protected $visible = ['id', 'name', 'arena_game_id'];

    public function arenaGame()
    {
        return $this->belongsTo(ArenaGame::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArenaGame extends Model
{
    /** @use HasFactory<\Database\Factories\ArenaGameFactory> */
    use HasFactory;

    protected $fillable = [
        'game_history_id',
        'pin',
        'start_time',
        'end_time',
        'status',
        'players_connected',
    ];

    public function gameHistory()
    {
        return $this->belongsTo(GameHistory::class);
    }
}

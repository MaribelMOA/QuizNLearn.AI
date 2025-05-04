<?php

use App\Models\ArenaGame;
use App\Models\ArenaPlayer;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('arena.{gameId}', function ($user, $gameId) {

    $playerName = session('player_name');
    $playerId = session('arena_player_id');

    if ($playerName && $playerId) {
        Log::info("Channel auth for gameId: $gameId, session name: $playerName, id: $playerId");

        return ['id' => $playerId, 'name' => $playerName];
    }


    // Si no hay datos en sesión, usa el nombre del usuario autenticado
    if ($user) {
        Log::info("Channel auth for gameId: $gameId, USER name: $user->name");

        return ['id' => $user->id, 'name' => $user->name,'is_host' => true];
    }

    return false;
});




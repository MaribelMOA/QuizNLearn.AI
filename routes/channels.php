<?php

use App\Models\ArenaGame;
use App\Models\ArenaPlayer;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//Broadcast::channel('arena.{arenaGameId}', function ($user, $arenaGameId) {
//    return ['id' => $user->id, 'name' => $user->name];
//});


Broadcast::channel('arena.{gameId}', function ($user, $gameId) {

    $playerName = session('player_name');
    $playerId = session('arena_player_id');

    if ($playerName && $playerId) {
        return ['id' => $playerId, 'name' => $playerName];
    }

    // Si no hay datos en sesión, usa el nombre del usuario autenticado
    if ($user) {
        return ['id' => $user->id, 'name' => $user->name,'is_host' => true];
    }

    return false;
});


//Broadcast::channel('arena.{arenaGameId}', function ($user, $arenaGameId) {
//    // Verifica que el usuario esté asociado al juego
//    $arenaGame = ArenaGame::find($arenaGameId);
//    return $arenaGame && $arenaGame->user_id === $user->id;
//});


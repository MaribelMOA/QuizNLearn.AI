<?php

namespace App\Events;

use App\Models\ArenaPlayer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PlayerJoined implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $player;
    public $arenaGameId;

    public function __construct(ArenaPlayer $player, $arenaGameId)
    {
        $this->player = $player;
        $this->arenaGameId = $arenaGameId;

        Log::info("Player joined: {$player->name}, Game ID: {$arenaGameId}");
    }

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel('arena.' . $this->arenaGameId);
    }

    public function broadcastAs(): string
    {
        return 'player.joined';
    }
}

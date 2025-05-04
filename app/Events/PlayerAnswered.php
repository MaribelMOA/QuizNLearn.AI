<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerAnswered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $arenaGameId;
    public $playerId;

    public function __construct($arenaGameId, $playerId)
    {
        $this->arenaGameId = $arenaGameId;
        $this->playerId = $playerId;
    }

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel('arena.' . $this->arenaGameId);
    }

    public function broadcastAs(): string
    {
        return 'player.answered';
    }
}

<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameEnded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $arenaGameId;
    public $results;

    public function __construct($arenaGameId, $results)
    {
        $this->arenaGameId = $arenaGameId;
        $this->results = $results;
    }

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel('arena.' . $this->arenaGameId);
    }

    public function broadcastAs(): string
    {
        return 'game.ended';
    }
}

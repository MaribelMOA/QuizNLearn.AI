<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Quiz;
use Illuminate\Support\Facades\Log;

class GameStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
   // public $quiz;
    public $arenaGameId;

    public function __construct($arenaGameId)
    {
        $this->arenaGameId = $arenaGameId;
        Log::info("GameStarted: arenaGameId=$arenaGameId");
    }

    public function broadcastOn()
    {
       // return new Channel('game-start.' . $this->arenaGameId);
        return new PresenceChannel("arena.{$this->arenaGameId}");
    }

    public function broadcastAs()
    {
        return 'game.started';  // Esto es crucial
    }


}

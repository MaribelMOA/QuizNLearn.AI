<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewQuestion implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $arenaGameId;
    public $question;

    public function __construct($arenaGameId, $question)
    {
        $this->arenaGameId = $arenaGameId;
        $this->question = $question;
    }

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel('arena.' . $this->arenaGameId);
    }

    public function broadcastAs(): string
    {
        return 'new.question';
    }
}

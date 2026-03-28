<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionEnded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    use Dispatchable, SerializesModels;

    public $arenaGameId;
    public $correctAnswer;
    public $stats; // puedes incluir estadísticas opcionales

    public function __construct($arenaGameId, $correctAnswer, $stats = [])
    {
        $this->arenaGameId = $arenaGameId;
        $this->correctAnswer = $correctAnswer;
        $this->stats = $stats;
    }

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel('arena.' . $this->arenaGameId);
    }

    public function broadcastAs(): string
    {
        return 'question.ended';
    }
}

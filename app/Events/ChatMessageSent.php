<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageData;
    public $chatRoomId;

    public function __construct(array $messageData, int $chatRoomId)
    {
        $this->messageData = $messageData;
        $this->chatRoomId = $chatRoomId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("chat.room.{$this->chatRoomId}");
    }

    public function broadcastWith()
    {
        return $this->messageData;
    }

    public function broadcastAs()
    {
        return 'ChatMessageSent';
    }
}

<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notificationData;
    public $recipientType;
    public $recipientId;

    public function __construct(array $notificationData, string $recipientType, int $recipientId)
    {
        $this->notificationData = $notificationData;
        $this->recipientType = $recipientType;
        $this->recipientId = $recipientId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("{$this->recipientType}.{$this->recipientId}");
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->notificationData['id'] ?? null,
            'title' => $this->notificationData['title'] ?? '',
            'message' => $this->notificationData['message'] ?? '',
            'type' => $this->notificationData['type'] ?? '',
            'created_at' => $this->notificationData['created_at'] ?? now()->toIso8601String(),
        ];
    }

    public function broadcastAs()
    {
        return 'NewNotification';
    }
}

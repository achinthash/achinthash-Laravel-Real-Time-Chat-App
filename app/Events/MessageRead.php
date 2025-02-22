<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageIds;
   // public $chatId;  // Chat ID for broadcasting to the correct conversation

    public function __construct($messageIds, )
    {
        $this->messageIds = $messageIds;
      //  $this->chatId = $chatId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->messageIds);
    }

    public function broadcastWith()
    {
        return [
            'message_ids' => $this->messageIds
        ];
    }
}

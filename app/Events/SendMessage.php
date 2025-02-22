<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Message;
use App\Models\MessageFile;

class SendMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $message;

    public function __construct(User $user,Message $message)
    {
        $this->message = $message;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->message->chat_id);
    }

    public function broadcastWith()
    {

        $file = MessageFile::where('message_id', $this->message->id)->first();

        return [
            'id' => $this->message->id,
            'chat_id' => $this->message->chat_id,
            'sender_id' => $this->message->sender_id,
            'content' => $this->message->content,
            'status' => $this->message->status,
            'message_type' => $this->message->message_type,
            'created_at' => $this->message->created_at->toDateTimeString(),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'file' => $file ? [
                'file_name' => $file->file_name,
                'file_path' =>  $file->file_path,
                'file_type' => $file->file_type,
                'file_size' => $file->file_size,
            ] : null,
        ];
    }
    
}


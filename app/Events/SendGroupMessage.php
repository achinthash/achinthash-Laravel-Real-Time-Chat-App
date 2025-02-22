<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\User;
use App\Models\group_members;
use App\Models\MessageFile;

class SendGroupMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user;
    public $groupMembers;

    public function __construct(Message $message, User $user , $groupMembers)
    {
        $this->message = $message;
        $this->user = $user;
        $this->groupMembers = collect($groupMembers); // Ensure it's always a collection

     
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->message->chat_id);
    }

    public function broadcastWith()
    {

        $role = $this->groupMembers->where('user_id', $this->user->id)->value('role') ?? 'unknown';
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
                'avatar' => $this->user->avatar,
            ],

            'role' => $role,

            'file' => $file ? [
                'file_name' => $file->file_name,
                'file_path' =>  $file->file_path,
                'file_type' => $file->file_type,
                'file_size' => $file->file_size,
            ] : null,
      
        ];
    }
}

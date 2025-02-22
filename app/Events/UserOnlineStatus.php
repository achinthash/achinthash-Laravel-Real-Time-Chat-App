<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserOnlineStatus implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $status;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param string $status
     */
    public function __construct(User $user, string $status)
    {
        $this->user = $user;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('online-users');
    }

    /**
     * Get the data to broadcast.UserOnlineStatusUpdated
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'user' => $this->user,
            'status' => $this->status,
        ];
    }
}

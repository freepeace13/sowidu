<?php

namespace App\Events\Chat;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class MessageDeleted implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $user;

    public $message;

    /**
     * Create a new event instance.
     *
     * @param  User|Employee  $user
     * @param  string  $message
     */
    public function __construct($user, $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('chat.conversation.' . $this->user->id);
    }
}

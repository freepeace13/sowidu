<?php

namespace App\Events\Todo\Task;

use App\Models\Employee;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCommentDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User|Employee $causer;

    public function __construct(
        public TaskComment $comment,
    ) {
        $this->causer = auth_user();
        $this->dontBroadcastToCurrentUser();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('tasks.' . $this->comment->task_id);
    }

    public function broadcastAs()
    {
        return 'task.comment';
    }
}

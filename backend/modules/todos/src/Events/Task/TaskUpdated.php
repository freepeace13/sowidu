<?php

namespace Modules\Todos\Events\Task;

use App\Models\Employee;
use App\Models\User;
use App\Support\Models\InteractsWithModelChanges;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Todos\Models\Task;

class TaskUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithModelChanges, InteractsWithSockets, SerializesModels;

    public $afterCommit = true;

    public array $changes;

    public User|Employee $causer;

    public function __construct(
        public Task $task,
    ) {
        $this->causer = auth_user();
        $this->changes = $this->getModelColumnChanges($task);
        $this->dontBroadcastToCurrentUser();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('boards.' . $this->task->board->id);
    }

    public function broadcastAs()
    {
        return 'board.task.updated';
    }
}

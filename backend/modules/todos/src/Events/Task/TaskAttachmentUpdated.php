<?php

namespace Modules\Todos\Events\Task;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Todos\Models\TaskAttachment;

class TaskAttachmentUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public TaskAttachment $taskAttachment,
    ) {
        $this->dontBroadcastToCurrentUser();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('tasks.' . $this->taskAttachment->task_id);
    }

    public function broadcastAs()
    {
        return 'task.attachment';
    }
}

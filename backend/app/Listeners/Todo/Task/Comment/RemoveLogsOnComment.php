<?php

namespace App\Listeners\Todo\Task\Comment;

use App\Events\Todo\Task\TaskCommentDeleted;
use App\Repositories\ActivityLog\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveLogsOnComment implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(TaskCommentDeleted $event)
    {
        (new ActivityLog($event->comment->task))->task()->commentDeleted($event->comment);
    }
}

<?php

namespace App\Listeners\Todo\Task\Comment;

use App\Events\Todo\Task\TaskCommentCreated;
use App\Repositories\ActivityLog\ActivityLog;

class LogTaskCommentCreated
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(TaskCommentCreated $event)
    {
        (new ActivityLog($event->comment->task))->task()->commentCreated($event->comment);
    }
}

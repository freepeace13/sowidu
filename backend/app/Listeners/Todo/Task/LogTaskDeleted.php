<?php

namespace App\Listeners\Todo\Task;

use App\Events\Todo\Task\TaskDeleted;
use App\Repositories\ActivityLog\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogTaskDeleted implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(TaskDeleted $event)
    {
        (new ActivityLog($event->task))->task()->deleted();
    }
}

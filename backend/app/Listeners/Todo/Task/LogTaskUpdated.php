<?php

namespace App\Listeners\Todo\Task;

use App\Events\Todo\Task\TaskUpdated;
use App\Repositories\ActivityLog\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogTaskUpdated implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(TaskUpdated $event)
    {
        (new ActivityLog($event->task))->task()->updated($event->changes);
    }
}

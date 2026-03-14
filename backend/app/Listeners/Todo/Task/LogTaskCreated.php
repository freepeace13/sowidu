<?php

namespace App\Listeners\Todo\Task;

use App\Events\Todo\Task\TaskCreated;
use App\Repositories\ActivityLog\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogTaskCreated implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(TaskCreated $event)
    {
        (new ActivityLog($event->task))->task()->created();
    }
}

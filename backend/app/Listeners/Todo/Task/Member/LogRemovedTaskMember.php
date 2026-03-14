<?php

namespace App\Listeners\Todo\Task\Member;

use App\Events\Todo\Task\TaskMemberRemoved;
use App\Repositories\ActivityLog\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogRemovedTaskMember implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(TaskMemberRemoved $event)
    {
        (new ActivityLog($event->task))->task()->removedMember($event->user, $event->causerHasLeft);
    }
}

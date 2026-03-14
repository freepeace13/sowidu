<?php

namespace App\Listeners\Todo\Task\Member;

use App\Events\Todo\Task\TaskMemberAdded;
use App\Repositories\ActivityLog\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogAddedTaskMember implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(TaskMemberAdded $event)
    {
        (new ActivityLog($event->task))->task()->addedMember($event->user, $event->causerHasJoined);
    }
}

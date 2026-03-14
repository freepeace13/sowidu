<?php

namespace App\Listeners\Todo\Task;

use App\Events\Todo\Task\TaskUpdated;
use App\Notifications\Todo\TaskIsMovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;

class NotifyMembersTaskGroupIsMoved implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(TaskUpdated $event)
    {
        if (!$group = Arr::get($event->changes, 'group', null)) {
            return;
        }

        $event->task->members()
            ->with('user')
            ->get()
            ->each(
                fn ($member) => $member->user->isNot($event->causer) ? Notification::send($member->user, new TaskIsMovedNotification($event->task, $event->causer, $group))
                    : null,
            );
    }
}

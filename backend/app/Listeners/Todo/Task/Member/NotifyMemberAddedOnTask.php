<?php

namespace App\Listeners\Todo\Task\Member;

use App\Events\Todo\Task\TaskMemberAdded;
use App\Notifications\Todo\Subscriber\SubscriberAssignedOnTaskNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyMemberAddedOnTask implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(TaskMemberAdded $event)
    {
        if ($event->user->is($event->causer)) {
            return true;
        }

        $event->user->notify(new SubscriberAssignedOnTaskNotification($event->task, $event->causer));
    }
}

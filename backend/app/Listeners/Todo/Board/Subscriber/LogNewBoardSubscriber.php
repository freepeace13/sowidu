<?php

namespace App\Listeners\Todo\Board\Subscriber;

use App\Events\Todo\Subscriber\BoardSubscriberAdded;
use App\Repositories\ActivityLog\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogNewBoardSubscriber implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(BoardSubscriberAdded $event)
    {
        if ($event->board->isPredefined()) {
            return;
        }

        (new ActivityLog($event->subscriber))->boardSubscriber()->added();
    }
}

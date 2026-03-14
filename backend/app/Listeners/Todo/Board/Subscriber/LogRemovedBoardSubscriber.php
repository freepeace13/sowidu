<?php

namespace App\Listeners\Todo\Board\Subscriber;

use App\Events\Todo\Subscriber\BoardSubscriberRemoved;
use App\Repositories\ActivityLog\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogRemovedBoardSubscriber implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(BoardSubscriberRemoved $event)
    {
        (new ActivityLog($event->subscriber))->boardSubscriber()->removed();
    }
}

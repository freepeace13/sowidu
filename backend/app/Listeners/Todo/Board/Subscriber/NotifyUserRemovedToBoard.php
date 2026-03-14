<?php

namespace App\Listeners\Todo\Board\Subscriber;

use App\Events\Todo\Subscriber\BoardSubscriberRemoved;
use App\Notifications\Todo\Subscriber\SubscriberRemovedToBoardNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyUserRemovedToBoard implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(BoardSubscriberRemoved $event)
    {
        $event->subscriber->user->notify(new SubscriberRemovedToBoardNotification($event->board));
    }
}

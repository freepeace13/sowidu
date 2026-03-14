<?php

namespace App\Listeners\Todo\Board\Subscriber;

use App\Events\Todo\Subscriber\BoardSubscriberAdded;
use App\Notifications\Todo\Subscriber\NewBoardSubscriberNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyUserAddedToBoard implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(BoardSubscriberAdded $event)
    {
        $event->subscriber->user->notify(new NewBoardSubscriberNotification($event->board));
    }
}

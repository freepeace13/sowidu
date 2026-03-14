<?php

namespace App\Listeners\Todo\Board;

use App\Events\Todo\BoardUpdated;
use App\Repositories\ActivityLog\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogBoardUpdated implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(BoardUpdated $event)
    {
        (new ActivityLog($event->board))->board()->updated($event->changes);
    }
}

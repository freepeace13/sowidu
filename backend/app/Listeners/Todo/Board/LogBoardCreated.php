<?php

namespace App\Listeners\Todo\Board;

use App\Events\Todo\BoardCreated;
use App\Repositories\ActivityLog\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogBoardCreated implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(BoardCreated $event)
    {
        if ($event->board->isPredefined()) {
            return;
        }

        (new ActivityLog($event->board))->board()->created();
    }
}

<?php

namespace App\Listeners\Todo\Board;

use App\Events\Todo\BoardDeleted;
use App\Repositories\ActivityLog\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ClearLogsForThisBoard implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(BoardDeleted $event)
    {
        (new ActivityLog($event->board))->board()->deleted();
    }
}

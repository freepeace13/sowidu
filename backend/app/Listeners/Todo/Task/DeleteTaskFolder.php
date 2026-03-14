<?php

namespace App\Listeners\Todo\Task;

use App\Events\Todo\Task\TaskDeleted;
use File;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteTaskFolder implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(TaskDeleted $event)
    {
        File::delete(public_path("todo/boards/{$event->task->board_id}/{$event->task->id}"));
    }
}

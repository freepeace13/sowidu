<?php

namespace App\Listeners\Todo\Task;

use App\Events\Todo\Task\TaskDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class DeleteTaskFiles implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(TaskDeleted $event)
    {
        Storage::deleteDirectory("todo/boards/{$event->task->board_id}/{$event->task->id}/");
    }
}

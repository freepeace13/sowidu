<?php

namespace App\Listeners\Todo\Task\Comment;

use App\Events\Todo\Task\TaskCommentCreated;
use App\Notifications\Todo\TaskCommentCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNewCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(TaskCommentCreated $event)
    {
        $event->comment->notifyOthers(
            new TaskCommentCreatedNotification($event->comment),
            $event->comment->author->user,
        );
    }
}

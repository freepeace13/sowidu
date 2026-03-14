<?php

namespace App\Notifications\Todo;

use App\Models\TaskComment;
use App\Transformers\Todo\BoardTransformer;
use App\Transformers\Todo\TaskCommentTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskCommentCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TaskComment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'comment' => (new TaskCommentTransformer($this->comment))->withTask()->resolve(),
            'board' => (new BoardTransformer($this->comment->task->board))->resolve(),
        ];
    }
}

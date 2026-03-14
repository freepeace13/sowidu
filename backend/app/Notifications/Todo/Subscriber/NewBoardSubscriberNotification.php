<?php

namespace App\Notifications\Todo\Subscriber;

use App\Models\Board;
use App\Transformers\Todo\BoardTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewBoardSubscriberNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Board $board) {}

    public function via($notifiable): array
    {
        return ['broadcast', 'database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'board' => (new BoardTransformer($this->board))->resolve(),
            'message' => __('todo.notifications.subscriber.added', [
                'board' => $this->board->title,
                'link' => route('todos.boards.task.tasks-group', ['board' => $this->board]),
            ]),
        ];
    }
}

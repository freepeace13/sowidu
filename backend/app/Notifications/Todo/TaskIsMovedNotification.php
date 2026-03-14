<?php

namespace App\Notifications\Todo;

use App\Models\Employee;
use App\Models\Task;
use App\Models\User;
use App\Transformers\Todo\BoardTransformer;
use App\Transformers\Todo\TaskTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskIsMovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Task $task, protected User|Employee $causer, protected array $changes) {}

    public function via($notifiable): array
    {
        return ['broadcast', 'database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'board' => (new BoardTransformer($this->task->board))->resolve(),
            'task' => (new TaskTransformer($this->task))->resolve(),
            'causer' => (new UserTransformer($this->causer))->resolve(),
            'changes' => $this->changes,
        ];
    }
}

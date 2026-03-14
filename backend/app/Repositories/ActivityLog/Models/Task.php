<?php

namespace App\Repositories\ActivityLog\Models;

use App\Models\Employee;
use App\Models\Task as Model;
use App\Models\TaskComment;
use App\Models\User;
use App\Support\Models\InteractsWithModelChanges;
use Spatie\Activitylog\Models\Activity;

class Task
{
    use InteractsWithModelChanges;

    protected $activity;

    protected array $properties;

    protected string $logName;

    public function __construct(
        protected Model $task,
        protected string $transKey = 'todo.activity.task',
    ) {
        $this->logName = "todo.board.{$task->board->id}";
        $this->properties = [
            'task' => $task->title,
            'group' => $task->group,
            'link' => route('todos.boards.tasks.show', ['task' => $task->id, 'board' => $this->task->board->id]),
        ];

        $this->activity = activity($this->logName)
            ->on($task)
            ->tap(function (Activity $activity) use ($task) {
                $activity->subject_type = get_class($task);
            })
            ->withProperties($this->properties);
    }

    public function created()
    {
        $this->activity
            ->event('created')
            ->withProperties(array_merge($this->properties, [
                'group' => $this->task->group,
            ]))
            ->log($this->transKey);
    }

    public function updated(array $changes = [])
    {
        collect($changes)
            ->map(function ($value, $column) {
                if (blank($column) || blank($value)) {
                    return;
                }

                activity($this->logName)
                    ->on($this->task)
                    ->tap(function (Activity $activity) {
                        $activity->subject_type = get_class($this->task);
                    })
                    ->withProperties($this->properties);
            });
    }

    public function deleted()
    {
        // Delete
        Activity::query()
            ->inLog($this->logName)
            ->forSubject($this->task)
            ->delete();

        $this->activity
            ->withProperties([
                'task' => $this->task->title,
                'group' => $this->task->group,
            ])
            ->event('deleted')
            ->log($this->transKey);
    }

    public function addedMember(User|Employee $user, bool $authUserJoined = false)
    {
        $this->activity
            ->withProperties(
                array_merge(
                    $this->properties,
                    [
                        'user' => $user->full_name,
                    ],
                ),
            )
            ->event('member.added')
            ->when($authUserJoined, function ($activity) {
                return $activity->event('member.added_auth_user');
            })
            ->log($this->transKey);
    }

    public function removedMember(User|Employee $user, bool $authUserHasLeft = false)
    {
        $this->activity
            ->withProperties(
                array_merge(
                    $this->properties,
                    [
                        'user' => $user->full_name,
                    ],
                ),
            )
            ->event('member.removed')
            ->when($authUserHasLeft, function ($activity) {
                return $activity->event('member.removed_auth_user');
            })
            ->log($this->transKey);
    }

    public function commentCreated(TaskComment $comment)
    {
        $this->activity
            ->withProperties(
                array_merge(
                    $this->properties,
                    [
                        'comment' => $comment->only('id'),
                    ],
                ),
            )
            ->event('comment.created')
            ->log($this->transKey);
    }

    public function commentDeleted(TaskComment $comment)
    {
        // Remove comment log
        Activity::query()
            ->where('properties->comment->id', $comment->id)
            ->inLog($this->logName)
            ->delete();
    }
}

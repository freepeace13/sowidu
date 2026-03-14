<?php

namespace Modules\Todos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Modules\Todos\Events\Task\TaskCommentCreated;
use Modules\Todos\Events\Task\TaskCommentDeleted;
use Modules\Todos\Events\Task\TaskCommentUpdated;

class TaskComment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'todo_task_comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_id',
        'author_id',
        'message',
    ];

    protected static function booted()
    {
        static::created(function ($comment) {
            TaskCommentCreated::dispatch($comment);
        });

        static::updated(function ($comment) {
            TaskCommentUpdated::dispatch($comment);
        });

        static::deleting(function ($comment) {
            TaskCommentDeleted::dispatch($comment);
        });
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function author()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function isOwner($user)
    {
        return $this->loadMissing('author.user')->author->user->is($user);
    }

    public function notifyOthers($instance, $owner)
    {
        $this->loadMissing('task.members.user')->task->members()
            ->with('user')
            ->get()
            ->each(fn ($member) => $member->user->isNot($owner) ? Notification::send($member->user, $instance)
                : null,
            );
    }
}

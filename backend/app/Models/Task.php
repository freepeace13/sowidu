<?php

namespace App\Models;

use App\Events\Todo\Task\TaskCreated;
use App\Events\Todo\Task\TaskDeleted;
use App\Events\Todo\Task\TaskMemberAdded;
use App\Events\Todo\Task\TaskMemberRemoved;
use App\Events\Todo\Task\TaskUpdated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Task extends Model
{
    use HasFactory, HasSlug;

    protected $table = 'todo_tasks';

    protected $fillable = [
        'title',
        'group',
        'description',
        'reporter_id',
        'task_id',
        'board_id',
    ];

    protected static function booted()
    {
        static::created(function ($task) {
            TaskCreated::dispatch($task);
        });

        static::updated(function ($task) {
            if ($task->isDirty(['title', 'group', 'description'])) {
                TaskUpdated::dispatch($task);
            }
        });

        static::deleting(function ($task) {
            TaskDeleted::dispatch($task);
        });
    }

    /**
     * Get the options for generating the slug on `title`.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('title')
            ->usingSeparator('-copy-')
            ->extraScope(fn ($builder) => $builder->where('board_id', $this->board_id));
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function parentTask()
    {
        return $this->belongsTo(static::class, 'task_id');
    }

    public function isSubTask()
    {
        return !is_null($this->task_id);
    }

    public function subtasks()
    {
        return $this->hasMany(self::class, 'task_id');
    }

    public function members()
    {
        return $this->belongsToMany(
            Subscriber::class,
            'todo_task_members',
            'task_id',
            'subscriber_id',
        );
    }

    public function addMember($user)
    {
        throw_validation_unless(
            $this->board->hasUser($user),
            'The user is not member of the board.',
        );

        throw_validation_if(
            $this->hasMember($user),
            'The user is already member of the task.',
        );

        $this->members()->attach(
            $this->board->getSubscription($user),
        );

        TaskMemberAdded::dispatch($this, $user);
    }

    public function removeMember(Subscriber $subscriber)
    {
        throw_validation_unless($this->members->contains($subscriber), 'This user is not a member of this task.');

        $this->members()->detach($subscriber);

        TaskMemberRemoved::dispatch($this, $subscriber->user);
    }

    public function hasMemberWithEmail($email)
    {
        return $this->members->contains(function ($member) use ($email) {
            return $member->user->email == $email;
        });
    }

    public function hasMember($user)
    {
        return $this->members->contains(function ($member) use ($user) {
            return $member->user_id == $user->getKey();
        });
    }

    public function reporter()
    {
        return $this->belongsTo(Subscriber::class);
    }

    /**
     * Scope a query to only include tasks belongs to `group`
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $group
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when(
            $keyword = $filters['q'] ?? null,
            fn ($q) => $q->where('title', 'like', "%$keyword%")
                ->orWhereHas(
                    'members.user',
                    fn ($q) => $q->search($keyword),
                ),
        )->when(
            $members = $filters['members'] ?? null,
            fn ($q) => $q->whereHas(
                'members',
                fn ($q) => $q->whereIn('subscriber_id', $members),
            ),
        )->when(
            $filters['unassigned'] ?? null,
            fn ($q) => $q->doesntHave('members'),
        )->when(
            $labels = $filters['labels'] ?? null,
            fn ($q) => $q->whereHas(
                'labels',
                fn ($q) => $q->whereIn('label_id', $labels),
            ),
        );
    }

    public function labels()
    {
        return $this->hasMany(TaskLabel::class);
    }

    /**
     * Add label to this task
     *
     *
     * @return TaskLabel
     */
    public function addLabel(int $labelId)
    {
        return TaskLabel::updateOrCreate([
            'task_id' => $this->id,
            'label_id' => $labelId,
        ]);
    }

    /**
     * Remove label to this task
     *
     *
     * @return TaskLabel
     */
    public function removeLabel(int $labelId)
    {
        return TaskLabel::where([
            'task_id' => $this->id,
            'label_id' => $labelId,
        ])->delete();
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function activities()
    {
        return Activity::query()
            ->with(['causer.profile.avatar'])
            ->inLog("todo.board.{$this->board->id}")
            ->where('subject_type', get_class($this))
            ->where('subject_id', $this->getKey());
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function timeLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TaskTimeLog::class);
    }

    public function timeLogsTotalDuration(): string
    {
        return $this->timeLogs()->selectRaw(
            'SEC_TO_TIME( SUM(TIME_TO_SEC(duration) ) ) as seconds',
        )->first()?->seconds ?? '00:00:00';
    }
}

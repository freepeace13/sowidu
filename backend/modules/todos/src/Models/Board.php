<?php

namespace Modules\Todos\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Todos\Database\Factories\BoardFactory;
use Modules\Todos\Events\BoardDeleted;
use Modules\Todos\Exceptions\BoardOwnerAlreadyExist;
use Modules\Todos\Exceptions\BoardSubscriberAlreadyExists;
use Modules\Todos\Exceptions\TeamUserNotExist;
use Modules\Todos\Support\BoardSettings;
use Spatie\Activitylog\Models\Activity;

class Board extends Model
{
    use HasFactory;

    const OWNER = 'owner';

    protected $table = 'todo_boards';

    protected $fillable = [
        'team_id',
        'title',
        'description',
        'logo_path',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    protected static function newFactory()
    {
        return BoardFactory::new();
    }

    protected static function booted()
    {
        static::created(function (self $board) {
            $board->settings()->saveDefault();
        });

        static::deleting(function ($board) {
            BoardDeleted::dispatch($board);
        });
    }

    public function settings(): BoardSettings
    {
        return new BoardSettings($this);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function subscribers()
    {
        return $this->hasMany(Subscriber::class);
    }

    public function getSubscription($user)
    {
        return $this->getSubscriber($user);
    }

    public function getSubscriber($user)
    {
        return $this->subscribers->first(function ($subscriber) use ($user) {
            return $subscriber->user_id === $user->getKey();
        });
    }

    public function addSubscriber($user, $role = 'guest')
    {
        $user->loadMissing(['teams']);

        if ($this->hasUser($user)) {
            throw new BoardSubscriberAlreadyExists;
        }

        if ($this->team && !$user->belongsToTeam($this->team)) {
            throw new TeamUserNotExist;
        }

        if ($this->owner() && $role == Subscriber::BOARD_OWNER_ROLE) {
            throw new BoardOwnerAlreadyExist;
        }

        $this->users()->attach($user->getKey(), ['role' => $role]);
    }

    public function team()
    {
        return $this->belongsTo(Company::class, 'team_id');
    }

    public function hasUser($user)
    {
        return $this->users->contains($user);
    }

    public function hasUserWithEmail(string $email)
    {
        return $this->users->contains(function ($user) use ($email) {
            return $user->email == $email;
        });
    }

    public function hasAnyRole($user, $roles)
    {
        if (!is_string($roles)) {
            $roles = [...$roles];
        }

        return in_array($this->userRole($user), $roles);
    }

    public function userRole($user)
    {
        if (!$this->hasUser($user)) {
            return null;
        }

        return $this->users->firstWhere('id', $user->getKey())->pivot->role;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, Subscriber::class)
            ->withPivot(['role', 'settings']);
    }

    public function subscriberOwner()
    {
        return $this->hasOne(Subscriber::class)->ofMany([
            'id' => 'max',
        ], function ($query) {
            $query->boardOwner();
        });
    }

    public function owner(): User
    {
        return $this->subscriberOwner->user;
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when(
            $keyword = $filters['q'] ?? null,
            fn ($q) => $q->where('title', 'like', "%$keyword%")
                ->orWhere('description', 'like', "%$keyword%"),
        );
    }

    public function activities()
    {
        return Activity::inLog("todo.board.{$this->id}");
    }

    public function scopeOrderByTaskLastUpdated(Builder $query, string $direction = 'desc')
    {
        $query->orderBy(
            Task::query()
                ->select('updated_at')
                ->whereColumn('todo_tasks.board_id', 'todo_boards.id')
                ->latest()
                ->take(1),
            $direction,
        );
    }

    /* Scope a query to only include pre-defined boards */
    public function scopePredefined(Builder $query)
    {
        return $query->where('settings->is_predefined', true);
    }

    public function isPredefined()
    {
        return $this->settings['is_predefined'] ?? false;
    }

    public function forTeam()
    {
        return $this->team_id;
    }
}

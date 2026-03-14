<?php

namespace App\Models;

use App\Contracts\Contactable as ContactableContract;
use App\Events\Employment\EmployeeLeaved;
use EloquentFilter\Filterable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Modules\Todos\Models\TodoManualTask;
use Musonza\Chat\Traits\Messageable;
use Packages\ActiveStatus\HasActiveStatus;
use Packages\MediaLibrary\HasMedia as HasMediaContract;

class Employee extends Model implements AuthorizableContract, ContactableContract, HasMediaContract
{
    use Authorizable,
        Concerns\HasMedia,
        Concerns\HasProfile,
        Concerns\HasRoles,
        Filterable,
        HasActiveStatus,
        HasFactory,
        Messageable,
        Notifiable,
        Relations\Confirmable,
        // Relations\HasRoles,
        Relations\Contactable,
        Relations\HasActiveState,
        Relations\Searchable,
        Relations\SendsInvitations,
        Relations\TaskMemberable;

    protected $searchable = [
        'relations' => ['user', 'employer'],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'role',
        'specialization_id',
        'invitation_id',
        'company_id',
        'confirmed',
        'position',
    ];

    /**
     * The guard name of the authorizable.
     *
     * @var string
     */
    protected $guard_name = 'commercial';

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    /**
     *  Setup model event hooks
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Always set UUID - don't check if it exists, just set it
            // Use setAttribute to ensure it's set even if there's a default
            $model->setAttribute('uuid', (string) Str::uuid());
        });

        self::deleting(function ($model) {
            event(new EmployeeLeaved($model->employer, $model));
        });
    }

    public function getParticipantDetails()
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->full_name,
            'photo' => $this->avatar_url,
            'is_member' => true,
            'role' => $this->role,
            'organization' => [
                'id' => $this->employer->getkey(),
                'name' => $this->employer->name,
                'photo' => get_company_avatar_url($this->employer),
            ],
        ];
    }

    public function getMediaPathRelativeToRoot(): string
    {
        $prefix = $this->user->getMediaPathRelativeToRoot();

        return $prefix . DIRECTORY_SEPARATOR . '@' . $this->employer->username;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    public function getAuthName()
    {
        return $this->user->getAuthName();
    }

    public function getAuthPhoto()
    {
        return $this->avatar_url;
    }

    public function getOrganizationName()
    {
        return $this->employer->name;
    }

    public function getOrganizationPhoto()
    {
        return $this->employer->avatar_url;
    }

    public function authenticator(): Authenticatable
    {
        return $this->employer;
    }

    // public function impersonator(): MorphOne
    // {
    //     return $this->morphOne(Client::class, 'model');
    // }

    public function getFirstNameAttribute()
    {
        return $this->user->first_name;
    }

    public function getLastNameAttribute()
    {
        return $this->user->last_name;
    }

    public function getFullNameAttribute()
    {
        return $this->user->full_name;
    }

    public function guardName()
    {
        return 'commercial';
    }

    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn()
    {
        // return "{$this->getMorphClass()}.{$this->id}";
        return "App.Models.Employee.{$this->id}";
    }

    public function authorizationGroup()
    {
        return $this->employer;
    }

    /**
     * Scope a query that only employee based on the given status
     */
    public function scopeStatus(Builder $query, $status = 'online', $test = null): Builder
    {
        return $query->where('active_status', $status);
    }

    public function getAvatarUrlAttribute()
    {
        return $this->user->avatar_url;
    }

    /**
     * Scope a query that only include employees that has specified user
     */
    public function scopeWhose(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Get employee user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\App\Models\User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function belongsToTeam($team)
    {
        if (is_null($team)) {
            return false;
        }

        return $team->id === $this->company_id;
    }

    /**
     * Get employee company employer
     */
    public function employer(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    /** @return HasOne|EmployeeRate */
    public function rate(): HasOne
    {
        return $this->hasOne(EmployeeRate::class);
    }

    public function manualTasks(): BelongsToMany
    {
        return $this->belongsToMany(TodoManualTask::class, 'employee_todo_manual_task');
    }
}

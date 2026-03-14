<?php

namespace App\Models;

use App\Models\Foundations\Organization;
use App\Notifications\VerifyEmail;
use EloquentFilter\Filterable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Musonza\Chat\Traits\Messageable;
use Packages\ActiveStatus\HasActiveStatus;
use Packages\MediaLibrary\HasMedia as HasMediaContract;
use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;

class User extends Organization implements AuthorizableContract, CanResetPasswordContract, HasMediaContract, MustVerifyEmailContract
{
    use Authorizable,
        CanResetPassword,
        Concerns\HasContacts,
        Concerns\HasLocale,
        Concerns\HasMedia,
        Concerns\HasPlaces,
        Concerns\HasProfile,
        Concerns\HasRoles,
        Concerns\HasTeams,
        Filterable,
        FindSimilarUsernames,
        HasActiveStatus,
        HasApiTokens,
        HasFactory,
        Messageable,
        MustVerifyEmail,
        // Relations\HasRoles,
        Notifiable,
        Relations\HasAvatar,
        Relations\HasCategories,
        Relations\HasOrders,
        Relations\ReceivesInvitations,
        Relations\Searchable,
        Relations\SendsInvitations;

    protected $searchable = [
        'columns' => ['first_name', 'last_name', 'email'],
    ];

    /**
     * The guard name of the authorizable.
     *
     * @var string
     */
    protected $guard_name = 'personal';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'gender',
        'mobile',
        'username',
        'password',
        'last_name',
        'first_name',
        'active_status',
        'confirmed',
        'uuid', // Add uuid to fillable to ensure it's included in mass assignment
    ];

    protected $appends = [
        'full_name',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            // Also check if UUID is empty string and set it
            // Use more robust check to catch empty strings, null, and unset values
            if (!isset($model->uuid) || $model->uuid === '' || $model->uuid === null || empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }

            // Always set username if not already set or if it's empty
            if (empty($model->username)) {
                $model->username = 'user_' . Str::random(8);
            }
        });
    }

    public function getParticipantDetails()
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->full_name,
            'photo' => get_user_avatar_url($this),
            'is_member' => false,
            'organization' => null,
        ];
    }

    public function getMediaPathRelativeToRoot(): string
    {
        return '@' . $this->username;
    }

    public function getAuthName()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAuthPhoto()
    {
        return $this->avatar_url;
    }

    public function getOrganizationName()
    {
        //
    }

    public function getOrganizationPhoto()
    {
        return $this->avatar_url;
    }

    public function authenticator(): Authenticatable
    {
        return $this;
    }

    // public function impersonator(): MorphOne
    // {
    //     return $this->morphOne(Client::class, 'model');
    // }

    public function guardName()
    {
        return 'personal';
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn()
    {
        // return "{$this->getMorphClass()}.{$this->id}";
        return "App.Models.User.{$this->id}";
    }

    /**
     * Passport special method to validate user api authentication
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function findForPassport(string $username)
    {
        return static::where('confirmed', true)
            ->where(function (Builder $query) use ($username) {
                $query->where('email', $username)
                    ->orWhere('username', $username)
                    ->orWhere('mobile', $username);
            })
            ->first();
    }

    /**
     * Route notifications for the Vonage channel.
     *
     * @return string
     */
    public function routeNotificationForVonage()
    {
        return $this->mobile;
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|string
     */
    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }

    /**
     * Get user's companies
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    /**
     * Get the user's employments
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employments(): HasMany
    {
        return $this->hasMany(Employee::class, 'user_id');
    }

    /**
     * Get the user's board where it subscribe
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany|Subscriber
     */
    public function subscribingToBoards(): HasMany
    {
        return $this->hasMany(Subscriber::class);
    }

    public function isVerified(): bool
    {
        return !is_null($this->email_verified_at) || (bool) $this->confirmed;
    }
}

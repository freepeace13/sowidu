<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Verification extends Model
{
    /**
     * Disable updated_at column
     *
     * @var mixed
     */
    const UPDATED_AT = null;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'vouch',
        'revoked',
        'message',
        'expires_at',
        'redirect_url',
        'verified_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['absolute_redirect_url'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();

            if (is_null($model->expires_at)) {
                $model->expires_at = now()->addMonth();
            }
        });
    }

    /**
     * Determine if the verification is via email or mobile
     *
     * @return bool
     */
    public function viaEmail()
    {
        return filter_var($this->vouch, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Get the notification/mailable class instance of verification
     *
     * @return mixed
     */
    public function getMessageInstance()
    {
        $class = $this->message;

        return new $class($this, $this->user);
    }

    /**
     * Create absolute url and append token query
     *
     * @return string
     */
    public function absoluteRedirectUrl()
    {
        return $this->redirect_url . '?token=' . $this->id;
    }

    /**
     * Determine if the token is expired
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->expires_at->lessThan(now());
    }

    /**
     * Determine if the token is already verified
     *
     * @return bool
     */
    public function isVerified()
    {
        return !is_null($this->verified_at);
    }

    /**
     * Determine if the verification is still valid
     *
     * @return bool
     */
    public function isValid()
    {
        return !$this->revoked;
    }

    /**
     * Get the valid verification of given user
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public static function findValidByUser(User $user)
    {
        return static::valid()
            ->vouch($user->mobile)->orWhere
            ->vouch($user->email)
            ->first();
    }

    /**
     * Revoke all verification tokens of current token user
     *
     * @return bool
     */
    public function revokeChildrenTokens()
    {
        return static::where(function (Builder $query) {
            $query->vouch($this->user->mobile)->orWhere->vouch($this->user->email);
        })
            ->where('id', '!=', $this->id)
            ->update(['revoked' => true]);
    }

    /**
     * Revoke the verification instance
     *
     * @return bool
     */
    public function revoke()
    {
        return $this->forceFill(['revoked' => true])->save();
    }

    /**
     * Verify if the given code matches the code of verification instance
     *
     * @param  string  $code
     * @return bool
     */
    public function verify($code = null)
    {
        if (Str::is($this->code, $code)) {
            return $this->forceFill(['verified_at' => now()])->save();
        }

        return false;
    }

    /**
     * Get the absolute redirect url value.
     *
     * @return string
     */
    public function getAbsoluteRedirectUrlAttribute()
    {
        return $this->absoluteRedirectUrl();
    }

    /**
     * Scope a query that only include verifications with the given vouch
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $vouch  email|phone
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeVouch(Builder $query, $vouch): Builder
    {
        return $query->where('vouch', $vouch);
    }

    /**
     * Scope a query that only include valid verifications
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->where('revoked', false);
    }

    /**
     * Scope a query that only include verified tokens
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Get the user that tent to verify
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class, 'vouch', $this->viaEmail() ? 'email' : 'mobile',
        );
    }
}

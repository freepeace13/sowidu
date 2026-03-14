<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContactRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ownerable_id',
        'ownerable_type',
        'user_id',
        'rejected_at',
        'accepted_at',
    ];

    /**
     * Disabling timestamp
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Specify the timestamp
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'accepted_at',
        'rejected_at',
    ];

    public function isPending()
    {
        return is_null($this->accepted_at) && is_null($this->rejected_at);
    }

    public function isRejected()
    {
        return !is_null($this->rejected_at);
    }

    /**
     * Scope a query that only include requests for specific user
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @param  App\Models\User  $user
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhich(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope a query that only include contact requests
     * that requested by employees of the company
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @param  App\Models\Company  $company
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwnByCompany(Builder $query, Company $company): Builder
    {
        return $query->whereHasMorph('requester',
            [Employee::class],
            function (Builder $query) use ($company) {
                $query->where('employees.company_id', $company->id);
            });
    }

    /**
     * Scope a query that only include contact requests
     * that requested by specified user
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @param  App\Models\User  $user
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwnByUser(Builder $query, User $user): Builder
    {
        return $query
            ->where('ownerable_id', $user->id)
            ->where('ownerable_type', $user->getMorphClass());
    }

    /**
     * Scope a query that only include pending requests
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query
            ->whereNull('accepted_at')
            ->whereNull('rejected_at');
    }

    /**
     * Get the requestor of the contact request
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function requester(): MorphTo
    {
        return $this->morphTo('ownerable');
    }

    public function sender(): MorphTo
    {
        return $this->morphTo('ownerable');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the requested user of the contact request
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

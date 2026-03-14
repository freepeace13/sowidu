<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmploymentRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'rejected_at',
        'accepted_at',
        'employee_id',
    ];

    /**
     * Disabled updated_at column
     *
     * @var string
     */
    const UPDATED_AT = null;

    /**
     * Set column that cast into dates
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'accepted_at',
        'rejected_at',
    ];

    /**
     * Determine if the employment request is already accepted or rejected
     *
     * @return bool
     */
    public function isActionTaken()
    {
        return !is_null($this->accepted_at) || !is_null($this->rejected_at);
    }

    public function isPending()
    {
        return is_null($this->accepted_at) && is_null($this->rejected_at);
    }

    public function isRejected()
    {
        return !is_null($this->rejected_at);
    }

    /**
     * Scope a query that only include employment requests that are still pending
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query
            ->whereNull('rejected_at')
            ->whereNull('accepted_at');
    }

    /**
     * Scope a query that only include employment requests that are rejected
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query
            ->whereNotNull('rejected_at')
            ->whereNull('accepted_at');
    }

    /**
     * Scope a query that only include employment requests that are accepted
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeAccepted(Builder $query): Builder
    {
        return $query
            ->whereNotNull('accepted_at')
            ->whereNull('rejected_at');
    }

    /**
     * Scope a query that only include employment requests that are
     * specifically requested to a user
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @param  App\Models\User  $user
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereCandidate(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Get the employee that sent the employment request
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the company of the employment request
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user of the employment request
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

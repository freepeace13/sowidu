<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * TODO: This model was not in used? please remove it together with its DB table
 */
class Status extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'title',
        'description',
        'color',
    ];

    /**
     * Define status keys
     *
     * @var string
     */
    const STATE_PREPARATION = 'preparation';

    const STATE_COMPLETED = 'completed';
    const STATE_PENDING = 'pending';
    const STATE_FINAL = 'final';
    const STATE_DONE = 'done';

    /**
     * Scope a query that only include statuses that has key of preparation
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopePreparation(Builder $query): Builder
    {
        return $query->key(self::STATE_PREPARATION);
    }

    /**
     * Scope a query that only include statuses that has key of completed
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->key(self::STATE_COMPLETED);
    }

    /**
     * Scope a query that only include statuses that has key of pending
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->key(self::STATE_PENDING);
    }

    /**
     * Scope a query that only include statuses that has key of final
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeFinal(Builder $query): Builder
    {
        return $query->key(self::STATE_FINAL);
    }

    /**
     * Scope a query that only include statuses that has key of done
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeDone(Builder $query): Builder
    {
        return $query->key(self::STATE_DONE);
    }

    /**
     * Scope a query that only include statuses that has key of cancelled
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->key(self::STATE_CANCELLED);
    }

    /**
     * Scope a query that only include statuses match given key
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeKey(Builder $query, string $key): Builder
    {
        return $query->where('key', $key);
    }
}

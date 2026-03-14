<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'billerable_id',
        'billerable_type',
        'ownerable_id',
        'ownerable_type',
        'contact_person_id',
    ];

    /**
     * Scope a query that only include customers with specified biller
     *
     * @param  mixed  $biller
     */
    public function scopeBillerable(Builder $query, $biller): Builder
    {
        return $query
            ->where('billerable_id', $biller->id)
            ->where('billerable_type', $biller->getMorphClass());
    }

    /**
     * Get the biller of the customer
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function biller(): MorphTo
    {
        return $this->morphTo('billerable');
    }

    /**
     * Get the contractor of the customer
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function owner(): MorphTo
    {
        return $this->morphTo('ownerable');
    }

    /**
     * Get the contact person of the customer
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contactPerson(): BelongsTo
    {
        return $this->belongsTo(CustomerContactPerson::class, 'contact_person_id');
    }
}

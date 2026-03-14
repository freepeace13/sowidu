<?php

namespace App\Models;

use App\Http\Resources\AddressResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @deprecated Use `\App\Models\Place` instead
 * @see \App\Models\Place
 */
class Address extends Model
{
    /**
     * Model table name
     *
     * @var string
     */
    protected $table = 'address';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ownerable_id',
        'ownerable_type',
        'street_id',
        'house_number_id',
        'city_id',
        'zipcode_id',
        'state_id',
        'country_id',
        'is_active',
    ];

    /**
     * Casts specified columns
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the complete address string
     *
     * @return string
     */
    public function getLabelAttribute()
    {
        $label = optional($this->houseNumber)->house_number;
        $label .= $this->street ? ", {$this->street->street_address}" : '';
        $label .= $this->state ? ", {$this->state->name}" : '';
        $label .= $this->city ? ", {$this->city->name}" : '';
        $label .= $this->country ? ", {$this->country->name}" : '';
        $label .= $this->zipcode ? " {$this->zipcode->code}" : '';

        return $label;
    }

    /**
     * Scope a query that only include addresses that is active
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query that order the addresses from newest to oldest
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeNewest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get the owner of the address
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function owner(): MorphTo
    {
        return $this->morphTo('ownerable');
    }

    /**
     * Get the address street
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class);
    }

    /**
     * Get the address house number
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function housenumber(): BelongsTo
    {
        return $this->belongsTo(HouseNumber::class, 'house_number_id');
    }

    /**
     * Get the address city
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the address zip code
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zipcode(): BelongsTo
    {
        return $this->belongsTo(Zipcode::class);
    }

    /**
     * Get the address state
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the address country
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return (new AddressResource($this))->resolve();
    }
}

<?php

namespace App\Models;

use App\Enums\PlaceType;
use App\Events\Place\PlaceSaved;
use App\Services\PlaceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Place extends Model
{
    use Concerns\HasCustomProperties;
    use HasFactory;

    const PUBLIC = 'public';

    protected $casts = [
        'custom_properties' => 'array',
        'is_private' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'label',
        'house_number',
        'street',
        'zipcode',
        'state',
        'city',
        'country',
        'country_name',
        'is_private',
        'owner_type',
        'owner_id',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            $placeService = new PlaceService;

            $model->type = $model->type ?? PlaceType::Others;

            $countryName = $placeService->findCountry($model->country)
                ?->name;
            if (!$countryName) {
                $country = $placeService->createCountry($model->country);
                $countryName = $country->name;

                $model->country = $country->id;
            }

            $model->country_name = $countryName;
        });

        static::saved(function ($model) {
            event(new PlaceSaved($model));
        });
    }

    public function getCompleteAddressAttribute()
    {
        $components = array_filter([
            $this->street,
            $this->house_number,
            $this->zipcode,
            $this->city,
            $this->country_name,
            $this->state,
        ]);

        return implode(', ', $components);
    }

    public function getShortFullAddressAttribute()
    {
        $components = array_filter([
            $this->street,
            $this->house_number,
            $this->zipcode,
            $this->city,
        ]);

        return implode(', ', $components);
    }

    public function setCountryAttribute($value)
    {
        $this->attributes['country'] = strtoupper($value);
    }

    public function toPrivate(bool $isPrivate = true)
    {
        $this->forceFill(['is_private' => $isPrivate]);

        return $this;
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeSearchFullAddress(Builder $query, $q)
    {
        return $query->whereRaw(
            "CONCAT_WS(', ', house_number, street, city, state, country_name) LIKE '%{$q}%'",
        );
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeDistinctFullAddress(Builder $query)
    {
        return $query->selectRaw(
            "*, CONCAT_WS(', ', house_number, street, city, state, country_name) as full_address",
        )
            ->groupBY('full_address');
    }

    public function scopePrivate(Builder $query)
    {
        return $query->where('is_private', true);
    }

    public function scopePublic(Builder $query)
    {
        return $query->where('is_private', false);
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}

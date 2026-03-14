<?php

namespace App\Transformers;

use App\Services\PlaceService;

/**
 * @property-read \App\Models\Place $resource
 */
class PlaceTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'house_number' => $this->resource?->house_number,
            'street' => $this->resource?->street,
            'state' => $this->resource?->state,
            'city' => $this->resource?->city,
            'country' => [
                'code' => $this->resource?->country,
                'name' => $this->resource?->country_name,
            ],
            'zipcode' => $this->resource?->zipcode,
            'full' => $this->resource?->complete_address,
        ];
    }

    public function withId()
    {
        return $this->state(function (array $attributes) {
            return [
                'id' => $this->resource?->id,
            ];
        });
    }

    public function withLabel()
    {
        return $this->state(function (array $attributes) {
            return [
                'label' => $this->resource?->label ?? 'No label',
            ];
        });
    }

    public function withSource()
    {
        return $this->state(function (array $attributes) {
            return [
                'source' => model_name($this->resource),
            ];
        });
    }

    public function withGoogleMapUrl(): self
    {
        return $this->state(fn () => [
            'google_map' => [
                'url' => (new PlaceService)->getGoogleMapUrl($this->resource),
            ],
        ]);
    }

    public function withShortFullAddress(): self
    {
        return $this->state(fn () => [
            'short_full_address' => $this->resource?->short_full_address,
        ]);
    }

    public function withOwnerable(): self
    {
        $this->resource?->loadMissing('owner');

        return $this->state(fn () => [
            'ownerable' => $this->resource->owner ?? null,
        ]);
    }
}

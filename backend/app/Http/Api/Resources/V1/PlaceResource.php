<?php

namespace App\Http\Api\Resources\V1;

use App\Services\PlaceService;
use Packages\RestApi\Resources\JsonResource;

class PlaceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'houseNumber' => $this->resource?->house_number,
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
            'googleMap' => [
                'url' => (new PlaceService)->getGoogleMapUrl($this->resource),
            ],
        ]);
    }

    public function withShortAddress(): self
    {
        return $this->state(fn () => [
            'shortAddress' => $this->resource?->short_full_address,
        ]);
    }
}

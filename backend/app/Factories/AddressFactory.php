<?php

namespace App\Factories;

use App\Contracts\Auth\AuthorizableGroup;
use Illuminate\Support\Arr;

class AddressFactory
{
    /**
     * @return \App\Models\Address
     */
    public static function make(AuthorizableGroup $addressable, array $attributes)
    {
        if (!static::isValid($attributes)) {
            return null;
        }

        $addressable->addresses()->update([
            'is_active' => false,
        ]);

        $attributes = array_merge($attributes, ['is_active' => true]);

        return $addressable->addresses()->create(
            Arr::only($attributes, [
                'street_id',
                'state_id',
                'city_id',
                'zipcode_id',
                'country_id',
                'house_number_id',
                'is_active',
            ]),
        );
    }

    /**
     * @return bool
     */
    public static function isValid(array $attributes)
    {
        $fields = [
            'street_id',
            'state_id',
            'city_id',
            'zipcode_id',
            'country_id',
            'house_number_id',
        ];

        return collect(array_keys($attributes))->contains(function ($item) {
            return in_array($item, [
                'street_id',
                'state_id',
                'city_id',
                'zipcode_id',
                'country_id',
                'house_number_id',
            ]);
        });
    }
}

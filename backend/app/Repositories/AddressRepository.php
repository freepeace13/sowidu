<?php

namespace App\Repositories;

use App\Contracts\Auth\AuthorizableGroup;

/**
 * @deprecated  Not used anymore! Feel free to delete.
 * @see \App\Services\PlaceService - used this instead
 */
class AddressRepository
{
    public static $mapModels = [
        'house_number' => \App\Models\HouseNumber::class,
        'city' => \App\Models\City::class,
        'zipcode' => \App\Models\Zipcode::class,
        'country' => \App\Models\Country::class,
        'state' => \App\Models\State::class,
        'street' => \App\Models\Street::class,
    ];

    public static $validationRules = [
        'patch' => [
            'address' => ['sometimes', 'array'],
            'address.street' => [
                'string',
                'required_with:address.reference.street_id',
            ],
            'address.house_number' => [
                'string',
                'required_with:address.reference.house_number_id',
            ],
            'address.city' => [
                'string',
                'required_with:address.reference.city_id',
            ],
            'address.zipcode' => [
                'string',
                'required_with:address.reference.zipcode_id',
            ],
            'address.country' => [
                'string',
                'required_with:address.reference.country_id',
            ],
            'address.state' => [
                'string',
                'required_with:address.reference.state_id',
            ],

            'address.reference' => [
                'state_id' => ['nullable', 'exists:states,id'],
                'street_id' => ['nullable', 'exists:streets,id'],
                'city_id' => ['nullable', 'exists:cities,id'],
                'country_id' => ['nullable', 'exists:countries,id'],
                'zipcode_id' => ['nullable', 'exists:zipcodes,id'],
                'house_number_id' => ['nullable', 'exists:house_numbers,id'],
            ],
        ],

        'store' => [
            'street_id' => ['exists:streets,id'],
            'state_id' => ['exists:states,id'],
            'country_id' => ['exists:countries,id'],
            'zipcode_id' => ['exists:zipcodes,id'],
            'house_number_id' => ['exists:house_numbers,id'],
            'city_id' => ['exists:cities,id'],
        ],
    ];

    public function mapKeyValueFromIds($keyIds = [])
    {
        return collect($keyIds)->reject(function ($value, $key) {
            return !array_key_exists($key, self::$mapModels);
        })->map(function ($id, $key) {
            $class = self::$mapModels[$key];

            if ($instance = $class::find($id)) {
                return [
                    'id' => $instance->id,
                    'value' => $instance->label(),
                ];
            }

            return null;
        })->all();
    }

    public function createFromArray(AuthorizableGroup $group, array $attributes)
    {
        $attributes = array_merge($attributes, ['is_active' => true]);

        return tap($group->addresses(), function ($builder) {
            $builder->update(['is_active' => false]);
        })->create($attributes);
    }
}

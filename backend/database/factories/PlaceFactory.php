<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Place>
 */
class PlaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type' => 'others',
            'label' => 'public',
            'house_number' => $this->faker->buildingNumber,
            'street' => $this->faker->streetName,
            'zipcode' => $this->faker->postcode,
            'state' => $this->faker->state,
            'city' => $this->faker->city,
            'country' => $this->faker->countryCode,
            'country_name' => $this->faker->country,
            'is_private' => false,
        ];
    }
}

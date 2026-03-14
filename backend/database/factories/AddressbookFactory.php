<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Addressbook>
 */
class AddressbookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'institution_type' => $this->faker->randomElement([0, 1]),
            'legalform' => $this->faker->word(),
            'foreign_type' => $this->faker->randomElement([0, 1]),
        ];
    }
}

<?php

namespace Modules\Catalog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Catalog\Models\CatalogItemUnit>
 */
class CatalogItemUnitFactory extends Factory
{
    protected $model = \Modules\Catalog\Models\CatalogItemUnit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Piece', 'Hour', 'Day', 'Kilogram', 'Liter', 'Meter']),
        ];
    }
}

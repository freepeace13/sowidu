<?php

namespace Modules\Catalog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CatalogItemTypeFactory extends Factory
{
    protected $model = \Modules\Catalog\Models\CatalogItemType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Tools', 'Services', 'Packages']),
        ];
    }
}

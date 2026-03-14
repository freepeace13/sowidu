<?php

namespace Modules\Invoicify\Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Invoicify\Models\InvoiceManualItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Invoicify\Models\InvoiceManualItem>
 */
class InvoiceManualItemFactory extends Factory
{
    protected $model = InvoiceManualItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $manualItemId = $this->faker->unique()->numberBetween(1000, 9999);

        return [
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'name' => $this->faker->words(3, true),
            'quantity' => $this->faker->randomFloat(2, 1, 10),
            'selling_price' => $this->faker->randomFloat(2, 10, 1000),
            'purchasing_price' => $this->faker->optional()->randomFloat(2, 5, 500),
            'description' => $this->faker->optional()->sentence(),
            'unit_name' => $this->faker->word(),
            'internal_id' => 'MI-' . crc32($manualItemId),
            'vendor_id' => $this->faker->word() . '-' . crc32($this->faker->numberBetween(1, 1000)),
        ];
    }
}

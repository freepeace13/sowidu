<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderProduct>
 */
class OrderProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'quantity' => $this->faker->randomFloat(2, 1, 10),
            'is_paid' => false,
            // Note: is_invoiced column doesn't exist in order_products table
            'details' => [
                'name' => $this->faker->words(3, true),
                'selling_price' => $this->faker->randomFloat(2, 10, 1000),
                'description' => $this->faker->optional()->sentence(),
            ],
        ];
    }
}

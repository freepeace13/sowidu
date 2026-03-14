<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'team_id' => Company::factory(),
            'clientable_id' => Company::factory(),
            'clientable_type' => (new Company)->getMorphClass(),
            'order_number' => $this->faker->unique()->numerify('ORD-####'),
            'type' => $this->faker->randomElement([1, 2]), // INCOMING_TYPE or OUTGOING_TYPE
            'status' => OrderStatus::IN_PREPARATION,
            'description' => $this->faker->sentence(),
            'order_date' => $this->faker->date(),
            'planned_start_date' => $this->faker->optional()->date(),
            'planned_finish_date' => $this->faker->optional()->date(),
        ];
    }
}

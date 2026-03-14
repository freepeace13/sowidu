<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkLog>
 */
class WorkLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = $this->faker->dateTimeBetween('-1 week', 'now');
        $endedAt = (clone $startedAt)->modify('+' . $this->faker->numberBetween(1, 8) . ' hours');
        $durationInSeconds = $endedAt->getTimestamp() - $startedAt->getTimestamp();

        return [
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'order_id' => Order::factory(),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'duration_in_seconds' => $durationInSeconds,
            'is_paid' => false,
            'is_invoiced' => false,
            'description' => $this->faker->optional()->sentence(),
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'ended_at' => $attributes['started_at'] ?? now(),
        ]);
    }
}

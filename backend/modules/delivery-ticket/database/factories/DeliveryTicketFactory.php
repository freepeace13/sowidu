<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Database\Factories;

use App\Enums\DeliveryTicketType;
use App\Models\Company;
use App\Models\Order;
use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\DeliveryTicket\Models\DeliveryTicket;

class DeliveryTicketFactory extends Factory
{
    protected $model = DeliveryTicket::class;

    public function definition(): array
    {
        return [
            'delivery_date' => $this->faker->date(),
            'external_id' => $this->faker->uuid(),
            'type' => DeliveryTicketType::INCOMING,
            'is_paid' => false,
            'total_selling_price' => 0,
            'total_purchasing_price' => 0,
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'order_id' => Order::factory(),
            'delivery_address_id' => Place::factory(),
        ];
    }

    public function incoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => DeliveryTicketType::INCOMING,
        ]);
    }

    public function outgoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => DeliveryTicketType::OUTGOING,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_paid' => true,
        ]);
    }
}

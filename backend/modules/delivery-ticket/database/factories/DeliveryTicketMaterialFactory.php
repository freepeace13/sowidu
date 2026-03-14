<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketMaterial;

class DeliveryTicketMaterialFactory extends Factory
{
    protected $model = DeliveryTicketMaterial::class;

    public function definition(): array
    {
        return [
            'delivery_ticket_id' => DeliveryTicket::factory(),
            'user_id' => User::factory(),
            'quantity' => $this->faker->numberBetween(1, 100),
            'purchasing_price' => $this->faker->randomFloat(2, 10, 1000),
            'selling_price' => $this->faker->randomFloat(2, 20, 2000),
            'is_paid' => false,
            'details' => collect(['name' => $this->faker->word()]),
        ];
    }
}

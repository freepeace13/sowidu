<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoicePayment>
 */
class InvoicePaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'payment_date' => $this->faker->date(),
            'method' => $this->faker->randomElement(PaymentMethod::values()),
            'reference_number' => $this->faker->uuid,
            'payer_name' => $this->faker->name,
            'notes' => $this->faker->text(30),
        ];
    }
}

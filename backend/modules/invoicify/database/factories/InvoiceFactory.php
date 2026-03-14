<?php

namespace Modules\Invoicify\Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Invoicify\Enums\InvoiceKind;
use Modules\Invoicify\Enums\InvoiceStatus;
use Modules\Invoicify\Enums\InvoiceType;
use Modules\Invoicify\Models\Invoice;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Invoicify\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'order_id' => \App\Models\Order::factory(),
            'biller_id' => Company::factory(),
            'biller_type' => (new Company)->getMorphClass(),
            'delivery_address_id' => \App\Models\Place::factory(),
            'type' => InvoiceType::OUTGOING,
            'kind' => InvoiceKind::PARTIAL_1,
            'status' => InvoiceStatus::DRAFT,
            'external_id' => $this->faker->optional()->numerify('EXT-####'),
            'notes' => $this->faker->optional()->sentence(),
            'delivery_date' => $this->faker->optional()->date(),
            'send_date' => null,
            'payment_date' => null,
            'biller_details' => [],
            'final_data' => null,
            'preview_layout' => null,
        ];
    }

    public function withoutOrder(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_id' => null,
        ]);
    }

    public function withOrder($order = null): static
    {
        return $this->state(fn (array $attributes) => [
            'order_id' => $order?->id ?? \App\Models\Order::factory(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::DRAFT,
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::SENT,
            'send_date' => now(),
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::PAID,
            'send_date' => now()->subDays(10),
        ]);
    }

    public function incoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => InvoiceType::INCOMING,
        ]);
    }

    public function outgoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => InvoiceType::OUTGOING,
        ]);
    }
}

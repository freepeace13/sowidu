<?php

namespace Modules\Invoicify\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Models\InvoiceItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Invoicify\Models\InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'user_id' => User::factory(),
            'name' => $this->faker->words(3, true),
            'quantity' => $this->faker->randomFloat(2, 1, 10),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->optional()->sentence(),
            'details' => [],
            'item_id' => \App\Models\OrderProduct::factory(),
            'item_type' => (new \App\Models\OrderProduct)->getMorphClass(),
        ];
    }
}

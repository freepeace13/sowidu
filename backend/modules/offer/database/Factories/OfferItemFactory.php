<?php

namespace Modules\Offer\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Offer\Models\Offer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Offer\Models\OfferItem>
 */
class OfferItemFactory extends Factory
{
    protected $model = \Modules\Offer\Models\OfferItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'quantity' => $this->faker->randomFloat(2, 1, 10),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->sentence(),
            'details' => [
                'category' => $this->faker->word(),
                'unit' => $this->faker->word(),
            ],
        ];
    }

    public function forOffer(Offer $offer)
    {
        return $this->state(fn () => [
            'offer_id' => $offer->id,
        ]);
    }
}

<?php

namespace Modules\Offer\Database\Factories;

use App\Enums\OfferType;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Offer\Models\Offer>
 */
class OfferFactory extends Factory
{
    protected $model = \Modules\Offer\Models\Offer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraphs(3, true),
            'type' => $this->faker->randomElement(OfferType::cases()),
            'offer_date' => $this->faker->dateTimeBetween('-3 months', '+1 month'),
        ];
    }

    public function forCompany(Company $company)
    {
        $addressbook = $company->addressbooks()
            ->inRandomOrder()
            ->first();

        $author = $company->employees()
            ->inRandomOrder()
            ->with('user')
            ->first();

        return $this->state(fn () => [
            'company_id' => $company->id,
            'recipientable_id' => $addressbook->id,
            'recipientable_type' => $addressbook->getMorphClass(),
            'user_id' => $author->user->id,
        ]);
    }
}

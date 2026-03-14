<?php

namespace Database\Factories;

use App\Actions\Catalog\CreateCatalogItemType;
use App\Models\Company;
use App\Models\User;
use App\Services\MediaFileService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * CatalogItem::factory()
 *           ->state([
 *               'user_id' => $request->user()->getKey(),
 *               'company_id' => $this->getCurrentTeamId(),
 *           ])
 *           ->media($this->getCurrentCompany())
 *           ->type($request->user(), $this->getCurrentCompany())
 *           ->count(1)
 *           ->create();
 */
class CatalogItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'catalog_item_type_id' => \App\Models\CatalogItemType::factory(),
            'name' => $this->faker->word(),
            'unit' => $this->faker->randomNumber(2),
            'purchasing_price' => $this->faker->randomFloat(2, 1, 100),
            'selling_price' => $this->faker->randomFloat(2, 1, 100),
            'description' => $this->faker->words($this->faker->numberBetween(3, 6), true),
        ];
    }

    public function media(Company $company)
    {
        $media = MediaFileService::makeForCompany($company)->inRandomOrder()->first();

        return $this->state(function (array $attributes) use ($media) {
            return [
                'media_id' => $media->id,
            ];
        });
    }

    public function type(User $user, Company $company)
    {
        $name = $this->faker->randomElement(['Tools', 'Services', 'Packages']);
        $type = CreateCatalogItemType::run($user, $company, $name);

        return $this->state(fn ($attributes) => [
            'catalog_item_type_id' => $type->id,
        ]);
    }
}

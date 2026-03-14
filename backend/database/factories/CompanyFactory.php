<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'institution_type_id' => 1,
            'legal_form_id' => 1,
            'confirmed' => true,
            'uuid' => (string) \Illuminate\Support\Str::uuid(), // Always set UUID in factory to avoid empty string issues
        ];
    }

    public function forUser(User $user)
    {
        return $this->state(fn () => [
            'user_id' => $user->id,
        ]);
    }
}

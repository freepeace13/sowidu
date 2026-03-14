<?php

namespace Modules\Todos\Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Todos\Models\Board;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Todos\Models\Board>
 */
class BoardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Board::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Create a company if none exists
        $company = Company::factory()->create();

        return [
            'team_id' => $company->id,
            'title' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'logo_path' => $this->faker->imageUrl(640, 480, 'business'),
            'settings' => $this->defaultSettings(),
        ];
    }

    /**
     * Generate default board settings as an array
     *
     * @return array<string, mixed>
     */
    protected function defaultSettings(): array
    {
        return [
            'groups' => config('todo.board.settings.groups.defaults', []),
            'labels' => config('todo.board.settings.labels.defaults', []),
            'permissions' => config('todo.board.settings.permissions.defaults', []),
        ];
    }
}

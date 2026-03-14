<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect(config('app.default.categories'))
            ->each(fn ($category) => Category::updateOrCreate([
                'name' => $category,
            ]));
    }
}

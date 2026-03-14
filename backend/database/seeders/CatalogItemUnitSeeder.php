<?php

namespace Database\Seeders;

use App\Models\CatalogItemUnit;
use Illuminate\Database\Seeder;

class CatalogItemUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect([1, 2, 3, 20, 10])
            ->each(fn ($unit) => CatalogItemUnit::updateOrCreate([
                'unit' => $unit,
            ]));
    }
}

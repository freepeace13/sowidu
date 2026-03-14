<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if (!Unit::where('name', 'pices')->exists()) {
            Unit::create([
                'name' => 'pices',
            ]);
        }
        if (!Unit::where('name', 'meters')->exists()) {
            Unit::create([
                'name' => 'meters',
            ]);
        }
        if (!Unit::where('name', 'packs')->exists()) {
            Unit::create([
                'name' => 'packs',
            ]);
        }

    }
}

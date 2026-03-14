<?php

namespace Database\Seeders;

use App\Models\ItemType;
use Illuminate\Database\Seeder;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if (!ItemType::where('name', 'working hour')->exists()) {
            ItemType::create([
                'name' => 'working hour',
            ]);
        }
        if (!ItemType::where('name', 'material')->exists()) {
            ItemType::create([
                'name' => 'material',
            ]);
        }
        if (!ItemType::where('name', 'car costs')->exists()) {
            ItemType::create([
                'name' => 'car costs',
            ]);
        }

    }
}

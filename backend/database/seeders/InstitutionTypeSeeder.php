<?php

namespace Database\Seeders;

use App\Models\InstitutionType;
use Illuminate\Database\Seeder;

class InstitutionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = collect([
            ['type' => 'Interprise', 'abbreviation' => 'INT'],
            ['type' => 'Club', 'abbreviation' => 'CLB'],
            ['type' => 'Organisation', 'abbreviation' => 'ORG'],
            ['type' => 'Community', 'abbreviation' => 'COM'],
            ['type' => 'Real Estate', 'abbreviation' => 'RE'],
        ]);

        $types->each(function ($type) {
            if (!InstitutionType::where('type', $type['type'])->exists()) {
                InstitutionType::create($type);
            }
        });
    }
}

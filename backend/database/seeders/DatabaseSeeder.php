<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(InstitutionTypeSeeder::class);
        $this->call(LegalFormSeeder::class);
        $this->call(SpecializationSeeder::class);
        $this->call(ItemTypeSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(StatusSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(SiteSettingSeeder::class);
        $this->call(UserSeeder::class);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Offer;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Company::find(2001);

        for ($i = 0; $i < 20; $i++) {
            Offer::factory()
                ->forCompany($company)
                ->create();
        }
    }
}

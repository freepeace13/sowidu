<?php

namespace Database\Seeders;

use App\Models\LegalForm;
use Illuminate\Database\Seeder;

class LegalFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $forms = collect([
            ['legal_form' => 'GmbH', 'abbreviation' => 'GMBH'],
            ['legal_form' => 'Book Club', 'abbreviation' => 'BC'],
            ['legal_form' => 'Real Estate', 'abbreviation' => 'RE'],
            ['legal_form' => 'OHG', 'abbreviation' => 'OHG'],
        ]);

        $forms->each(function ($form) {
            if (!LegalForm::where('legal_form', $form['legal_form'])->exists()) {
                LegalForm::create($form);
            }
        });
    }
}

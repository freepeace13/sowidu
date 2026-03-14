<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $specializations = collect([
            ['title' => 'CEO', 'description' => 'Chief Executive Officer'],
            ['title' => '2. CEO', 'description' => 'Assistant Chief Executive Officer'],
            ['title' => 'Employee', 'description' => 'Regular Employee'],
            ['title' => 'Manager', 'description' => 'Department Manager'],
            ['title' => 'Chairman', 'description' => 'Chairman of the board'],
        ]);

        $specializations->each(function ($specialization) {
            if (!Specialization::where('title', $specialization['title'])->exists()) {
                Specialization::create($specialization);
            }
        });
    }
}

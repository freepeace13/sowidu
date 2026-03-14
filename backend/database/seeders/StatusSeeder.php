<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Status::firstOrCreate([
            'key' => 'preparation', 'title' => 'In Preparation', 'color' => '#546E7A',
        ]);

        Status::firstOrCreate([
            'key' => 'completed', 'title' => 'Completed', 'color' => '#C0CA33',
        ]);

        Status::firstOrCreate([
            'key' => 'pending', 'title' => 'Pending', 'color' => '#7CB342',
        ]);

        Status::firstOrCreate([
            'key' => 'final', 'title' => 'Final', 'color' => '#43A047',
        ]);

        Status::firstOrCreate([
            'key' => 'done', 'title' => 'Done', 'color' => '#039BE5',
        ]);

        Status::firstOrCreate([
            'key' => 'cancelled', 'title' => 'Cancelled', 'color' => '#E53935',
        ]);
    }
}

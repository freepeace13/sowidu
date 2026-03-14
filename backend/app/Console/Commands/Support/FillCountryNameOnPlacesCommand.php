<?php

namespace App\Console\Commands\Support;

use App\Models\Place;
use Illuminate\Console\Command;
use Packages\Countries\Countries;

class FillCountryNameOnPlacesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:fill-country-name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will fill country_name column on places table.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Place::all()
            ->each(function (Place $place) {
                $countryName = Countries::find($place->country)->name;
                if (
                    $place->update([
                        'country_name' => $countryName,
                    ])
                ) {
                    $this->info("Saving $countryName .");
                } else {
                    $this->error("Error encountered! While converting $countryName");
                }
            });

        return 0;
    }
}

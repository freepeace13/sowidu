<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use PragmaRX\Countries\Package\Countries;

class CountrySync extends Command
{
    protected $signature = 'country:sync';

    protected $description = 'Synchronize country, states and cities data from sources.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->error('This command is no longer supported. Use Place model/table instead.');

        return Command::FAILURE;

        $countries = new Countries;

        $this->info('Synchronizing countries data...');
        $bar = $this->output->createProgressBar(count($countries->all()));
        $bar->start();

        $countries->all()->each(function ($source) use ($bar) {
            $name = $source->name->common;
            $states = $source->hydrateStates()->states;

            $abbrev = $source['abbrev'] ? $source->abbrev : null;
            $country = Country::firstOrCreate(['name' => $name]);
            $country->update(['abbrev' => $abbrev]);

            $states->each(function ($state) use ($country, $source) {
                if (gettype($state) == 'object' && $state['name']) {
                    $state = $country->states()->firstOrCreate(['name' => $state->name]);
                    $cities = $source->hydrateCities()
                        ->cities
                        ->where('adm1name', $state->name);
                    $cities->each(function ($city) use ($state) {
                        $state->cities()->firstOrCreate(['name' => $city->name]);
                    });
                }
            });

            $bar->advance();
        });

        $bar->finish();

        return 0;
    }
}

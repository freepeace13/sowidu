<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use PragmaRX\Countries\Package\Countries;

class MigrateAddressToPlaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sw:migrate-address-to-places';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will migrate old address handling to place handling.';

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
        $this->migrateUsersAddressesToPlaces();
        $this->migrateCompaniesAddressesToPlaces();

        return 0;
    }

    protected function migrateUsersAddressesToPlaces()
    {
        $users = User::has('addresses')->with(['addresses', 'ownedPlaces'])->get();

        $this->info("\nMigrating {$users->count()} users places...\n");

        $this->withProgressBar($users, function ($user) {
            foreach ($user->addresses as $address) {
                $this->addModelPlace($user, $user->fullName, $address);
            }
        });

        $this->info("\nMigrated successfully.\n");
    }

    protected function migrateCompaniesAddressesToPlaces()
    {
        $companies = Company::has('addresses')->with(['addresses', 'ownedPlaces'])->get();

        $this->info("\nMigrating {$companies->count()} companies places...\n");

        $this->withProgressBar($companies, function ($company) {
            foreach ($company->addresses as $address) {
                $this->addModelPlace($company, $company->name, $address);
            }
        });

        $this->info("\nMigrated successfully.\n");
    }

    protected function addModelPlace($model, $label, $address)
    {
        $country = Countries::all()->where('name.common', $address->country)->first();

        $model->addPlace($label, [
            'house_number' => $address->house_number,
            'street' => $address->street,
            'state' => $address->state,
            'city' => $address->city,
            'country' => $country?->cca2,
        ], false);
    }
}

<?php

namespace App\Providers;

use App\Models\Addressbook;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Packages\Urn\UrnManager;

class UrnServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        UrnManager::register('person', User::class);
        UrnManager::register('organization', Company::class);
        UrnManager::register('organizationMember', Employee::class);
        UrnManager::register('addressbook', Addressbook::class);
    }
}

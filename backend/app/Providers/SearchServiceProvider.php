<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use App\Registrars\AggregateSearchRegistrar;
use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    const QUERY_NAME = 'aggregates';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AggregateSearchRegistrar::class, function () {
            return new AggregateSearchRegistrar([
                'users' => User::class,
                'companies' => Company::class,
                'employees' => Employee::class,
            ]);
        });
    }
}

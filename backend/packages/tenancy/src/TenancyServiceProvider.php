<?php

namespace Packages\Tenancy;

use Illuminate\Support\ServiceProvider;

class TenancyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // TestGuardStore is automatically used by TeamStoreFactory
        // when running in testing environment, so no explicit binding needed
    }

    public function boot(): void
    {
        //
    }
}

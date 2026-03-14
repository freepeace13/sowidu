<?php

namespace Packages\Addressable;

use Illuminate\Support\ServiceProvider;

class AddressServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/address.php', 'address');
    }

    public function boot()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/address.php' => config_path('address.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations/2021_11_16_000000_create_addresses_table.php' => database_path('migrations/2021_11_16_000000_create_addresses_table.php'),
        ], 'migrations');
    }
}

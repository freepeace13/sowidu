<?php

namespace Packages\ActiveStatus;

use Illuminate\Support\ServiceProvider;
use Packages\ActiveStatus\Console\CreateMiddleware;
use Packages\ActiveStatus\Console\CreateMigration;
use Packages\ActiveStatus\Console\SwitchStatus;

class ActiveStatusServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateMigration::class,
                CreateMiddleware::class,
                SwitchStatus::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../config/activestatus.php' => config_path('activestatus.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/activestatus.php', 'activestatus');
    }
}

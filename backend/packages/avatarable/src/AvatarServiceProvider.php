<?php

namespace Packages\Avatarable;

use Illuminate\Support\ServiceProvider;

class AvatarServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/avatar.php', 'avatar');
    }

    public function boot()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/avatar.php' => config_path('avatar.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations/2021_11_15_000000_create_avatarables_table.php' => database_path('migrations/2021_11_15_000000_create_avatarables_table.php'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../stubs/avatar.png' => public_path('images/avatar.png'),
        ], 'images');
    }
}

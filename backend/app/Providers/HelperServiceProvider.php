<?php

namespace App\Providers;

use App\Helpers\Index;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('app.helper', function ($app) {
            return new Index;
        });
        $this->app->alias('app.helper', Index::class);
    }
}

<?php

namespace App\Providers;

use Illuminate\Broadcasting\BroadcastController;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Support\ServiceProvider;

class WebSocketServiceProvider extends ServiceProvider
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
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        $this->app['router']->group(['middleware' => ['web']], function ($router) {
            $router->match(
                ['get', 'post'],
                '/apps/broadcasting/auth',
                BroadcastController::class . '@authenticate',
            );
        });
    }
}

<?php

namespace Modules\DeliveryTicket\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // JSON routes
        Route::group([
            'prefix' => 'json/' . config('deliveryticket.prefix'),
            'middleware' => config('deliveryticket.json_middleware'),
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/json.php');
        });

        Route::group([
            'prefix' => config('deliveryticket.prefix'),
            'middleware' => config('deliveryticket.middleware'),
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        });
    }
}

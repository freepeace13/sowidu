<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Passport::routes(function ($router) {
        //     $router->forAccessTokens();
        // });

        // $this->mapAdminRoutes();
    }

    protected function mapAdminRoutes()
    {
        // Route::group(['prefix' => 'admin'], base_path('routes/admin.php'));
    }
}

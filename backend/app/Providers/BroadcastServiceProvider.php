<?php

namespace App\Providers;

use App\Factories\Account;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $guards = implode(',', Account::$guards);
        Broadcast::routes(['middleware' => ['api', "auth:{$guards}"]]);

        Broadcast::routes([
            'middleware' => ['web', 'auth'],
            'prefix' => 'apps',
        ]);

        require base_path('routes/channels.php');
    }
}

<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $middleware = array_merge(
            config('worklog.middleware', []),
            [
                'permission:' . config('worklog.permissions.can_access_work_logs'),
            ],
        );

        Route::group([
            'prefix' => config('worklog.prefix'),
            'as' => 'work_logs.',
            'middleware' => $middleware,
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        });
    }
}

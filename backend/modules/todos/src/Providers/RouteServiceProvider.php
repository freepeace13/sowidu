<?php

namespace Modules\Todos\Providers;

use App\Enums\Permissions;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Todos\Http\Middleware\TodoHandleInertiaRequests;

class RouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Route::name('todos.')
            ->prefix(config('todos.prefix'))
            ->middleware([
                'web',
                'auth',
                'permission:' . Permissions::CAN_ACCESS_TODO,
                TodoHandleInertiaRequests::class,
            ])->group(base_path('modules/todos/routes/web.php'));
    }
}

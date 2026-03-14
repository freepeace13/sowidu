<?php

use App\Http\Controllers\Inertia\Account\AddressController;
use App\Http\Controllers\Inertia\Account\NotificationController;
use App\Http\Controllers\Inertia\Account\ReadAllNotificationController;
use App\Http\Controllers\Inertia\Ajax\GeoLocationController;
use App\Http\Controllers\Inertia\DesktopController;
use App\Http\Controllers\Inertia\EmployeeController;
use App\Http\Controllers\Inertia\LockScreenController;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    HandleInertiaRequests::class,
])->group(function () {
    Route::post('/lockscreen/activate', [LockScreenController::class, 'activate'])
        ->name('lockscreen.activate');

    Route::post('/lockscreen/deactivate', [LockScreenController::class, 'deactivate'])
        ->name('lockscreen.deactivate');

    Route::get('/desktop', [DesktopController::class, 'create'])
        ->name('desktop')
        ->middleware(['auth', 'verified']);

    Route::prefix('notifications')->name('account.notifications.')->group(function () {
        Route::get('all', [NotificationController::class, 'index'])->name('all');

        Route::put('{notification}/read', [
            NotificationController::class, 'read',
        ])->name('read');

        Route::post('read-all', ReadAllNotificationController::class)->name('read_all');
    });
});

Route::get('/ajax/geolocation', [GeoLocationController::class, 'search'])
    ->name('ajax.geolocation.search');

Route::get('/countries/{country}/states', [AddressController::class, 'states'])
    ->name('countries.states');

Route::get('/apps/employees/users', [EmployeeController::class, 'users'])
    ->name('apps.employees.users')
    ->middleware('auth');

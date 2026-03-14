<?php

use App\Http\Controllers\Json\Organization\OrganizationController;
use App\Http\Controllers\Json\Person\PersonController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'json',
    'middleware' => ['auth', 'json'],
], function () {
    Route::get('/person', [PersonController::class, 'index'])
        ->name('json.person.index');

    Route::get('/person/{id}', [PersonController::class, 'show'])
        ->name('json.person.show');

    Route::get('/organization', [OrganizationController::class, 'index'])
        ->name('json.organization.index');

    Route::get('/organization/{id}', [OrganizationController::class, 'show'])
        ->name('json.organization.show');

    require __DIR__ . '/json/autocomplete.php';
    require __DIR__ . '/json/place.php';
    require __DIR__ . '/json/addressbook.php';
    require __DIR__ . '/json/profile.php';
    require __DIR__ . '/json/media.php';
    require __DIR__ . '/json/order.php';
    require __DIR__ . '/json/work_log.php';
    require __DIR__ . '/json/catalog.php';
    require __DIR__ . '/json/delivery_ticket.php';
    require __DIR__ . '/json/invoice.php';
});

require __DIR__ . '/json/public.php';

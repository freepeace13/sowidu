<?php

use App\Http\Controllers\AppSettings\TranslationManagerController;
use App\Http\Controllers\AppSettings\UserManagerController;
use App\Http\Controllers\Inertia\AppSettings\AddressRecordController;
use App\Http\Controllers\Inertia\AppSettings\AppSettingsIndexController;
use App\Http\Controllers\Inertia\AppSettings\CatalogUnitsController;
use App\Http\Middleware\IsSuperAdmin;
use App\Http\Middleware\Web\AppSettingsInertiaRequestsHandler;

Route::prefix('app/settings')
    ->middleware([
        'auth',
        IsSuperAdmin::class,
        AppSettingsInertiaRequestsHandler::class,
    ])
    ->group(function () {
        Route::get('', AppSettingsIndexController::class)->name('app.settings.index');

        // /app/settings/catalogs-units/manage/*
        Route::controller(CatalogUnitsController::class)
            ->prefix('catalogs-units/manage')
            ->group(function () {
                Route::get('', 'index')->name('app.settings.catalogs.units');
                Route::post('store', 'store')->name('app.settings.catalogs.units.store');
                Route::patch('{unit}/update', 'update')->name('app.settings.catalogs.units.update');
                Route::delete('{unit}/destroy', 'destroy')->name('app.settings.catalogs.units.destroy');
            });

        // /app/settings/translations/manage/*
        Route::controller(TranslationManagerController::class)
            ->prefix('translations/manage')
            ->group(function () {
                Route::get('', 'index')->name('app.settings.translation-manager');
                Route::post('store', 'store')->name('app.settings.translation-manager.store');
            });

        // /app/settings/users/manage/*
        Route::controller(UserManagerController::class)
            ->prefix('users/manage')
            ->group(function () {
                Route::get('', 'index')->name('app.settings.manager.users');
                Route::delete('block/{user}', 'block')->name('app.settings.manager.users.block');
            });

        // /app/settings/address/manage/*
        Route::controller(AddressRecordController::class)
            ->prefix('addresses/manage')
            ->group(function () {
                Route::get('', 'index')->name('app.settings.addresses.manage');
                Route::post('store', 'store')->name('app.settings.addresses.store');
                Route::patch('{place}/update', 'update')->name('app.settings.addresses.patch');
            });
    });

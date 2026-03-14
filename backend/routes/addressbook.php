<?php

use App\Enums\Permissions;
use App\Http\Controllers\Inertia\Addressbook\AddressbookOrganizationController;
use App\Http\Controllers\Inertia\Addressbook\AddressbookOrganizationMemberController;
use App\Http\Controllers\Inertia\Addressbook\AddressbookPersonController;
use App\Http\Controllers\Inertia\Addressbook\CareOfController;
use App\Http\Controllers\Inertia\Addressbook\CreateForeignAddressbookPersonController;
use App\Http\Controllers\Inertia\Addressbook\CreateForeignOrganizationAddressbookController;
use App\Http\Controllers\Inertia\Addressbook\StoreNewAddressController;
use App\Http\Controllers\Inertia\Addressbook\Trash\AddressbookTrashController;
use App\Http\Controllers\Inertia\Addressbook\Trash\DeleteForeverAddressbookController;
use App\Http\Controllers\Inertia\Addressbook\Trash\RestoreAddressbookController;
use App\Http\Middleware\Web\AddressbookHandleInertiaRequests;
use Illuminate\Support\Facades\Route;

Route::prefix('apps/addressbooks')
    ->name('addressbooks.')
    ->middleware([
        'auth',
        'permission:' . Permissions::CAN_ACCESS_ADDRESS_BOOK,
        AddressbookHandleInertiaRequests::class,
    ])
    ->group(function () {
        Route::resource('people', AddressbookPersonController::class)
            ->except(['create', 'edit']);

        Route::post('people/foreign', CreateForeignAddressbookPersonController::class)
            ->name('people.foreign.store');

        Route::resource('organizations', AddressbookOrganizationController::class)
            ->except(['create', 'edit']);

        Route::post('organizations/foreign', CreateForeignOrganizationAddressbookController::class)
            ->name('organization.foreign.store');

        Route::apiResource(
            'organizations.members',
            AddressbookOrganizationMemberController::class,
        )->only(['store', 'update', 'destroy']);

        // addressbooks.addresses.*
        Route::post('addresses', StoreNewAddressController::class)
            ->name('addresses.store');

        Route::put('{addressbook}/attach/careof', [CareOfController::class,  'update'])->name('careof.update');

        // addressbooks.trashes.*
        Route::prefix('trashes')->name('trashes.')
            ->group(function () {
                Route::get('', AddressbookTrashController::class)->name('index');
                Route::put('{addressbook}/restore', RestoreAddressbookController::class)->name('restore');
                Route::delete('{addressbook}/delete', DeleteForeverAddressbookController::class)
                    ->name('destroy');
            });
    });

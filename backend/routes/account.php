<?php

use App\Enums\Permissions;
use App\Http\Controllers\Inertia\Account\AccountAddressController;
use App\Http\Controllers\Inertia\Account\AccountController;
use App\Http\Controllers\Inertia\Account\CategoryController;
use App\Http\Controllers\Inertia\Account\ProfileController;
use App\Http\Controllers\Inertia\Organization\Employee\UpdateEmployeeRateController;
use App\Http\Controllers\Inertia\Organization\EmployeeController;
use App\Http\Controllers\Inertia\Organization\EmployeeInvitationController;
use App\Http\Controllers\Inertia\Organization\EmployeeLeaveOrganizationController;
use App\Http\Controllers\Inertia\Organization\EmployeePermissionController;
use App\Http\Controllers\Inertia\Organization\InviteeResponseController;
use App\Http\Controllers\Inertia\Organization\ManageEmployeeAccessController;
use App\Http\Controllers\Inertia\Organization\OrganizationController;
use App\Http\Controllers\Inertia\Organization\RoleController;
use App\Http\Controllers\Inertia\Organization\Settings\InvoiceSettingsController;
use App\Http\Controllers\Inertia\Organization\Settings\OrganizationMediaSettingsController;
use App\Http\Controllers\Inertia\Organization\TaxSettingsController;
use App\Http\Controllers\Inertia\Organization\UpdateTaxNumberController;
use App\Http\Controllers\Inertia\Organization\UpdateVatIdentificationNumberController;
use App\Http\Middleware\Web\AccountHandleInertiaRequests;
use Illuminate\Support\Facades\Route;
use Modules\Offer\Controllers\OfferConfigurationController;

Route::name('account.')
    ->prefix('/account')
    ->middleware([
        'auth',
        AccountHandleInertiaRequests::class,
    ])
    ->group(function () {
        Route::put('update', [AccountController::class, 'update'])
            ->name('update');

        Route::post('employees/{employee}/rates/store', UpdateEmployeeRateController::class)
            ->name('employees.rates.store');

        Route::resource('profile', ProfileController::class, [
            'parameters' => ['profile' => 'profile?'],
        ])->only(['index', 'update']);

        Route::resource('employees', EmployeeController::class)
            ->only(['index', 'update']);

        Route::prefix('employees/invitations')
            ->name('employees.invitations')
            ->group(function () {
                Route::controller(EmployeeInvitationController::class)
                    ->group(function () {
                        Route::get('{status}', 'index')->where('status', 'pending|failed');
                        Route::post('', 'store')->name('.store');
                        Route::post('{token}/cancel', 'destroy')->name('.cancel');
                    });

                Route::controller(InviteeResponseController::class)
                    ->group(function () {
                        Route::post('{token}/accept', 'accept')->name('.accept');
                        Route::post('{token}/decline', 'decline')->name('.decline');
                    });
            });

        Route::controller(EmployeePermissionController::class)
            ->prefix('access/roles')
            ->name('access.roles.')
            ->group(function () {
                Route::get('{role}/permissions', 'index')->name('permissions');
                Route::put('{role}/permissions', 'update')->name('permissions.update');
            });

        Route::get('access', ManageEmployeeAccessController::class)->name('access');

        Route::controller(OrganizationController::class)
            ->prefix('organizations')
            ->name('organizations.')
            ->group(function () {
                Route::get('', 'index')->name('index');
                Route::post('authorize', 'authorize')->name('authorize');
                Route::delete('logout', 'logout')->name('logout');
                Route::post('', 'store')->name('store');

                Route::post(
                    '{company}/leave',
                    EmployeeLeaveOrganizationController::class,
                )->name('leave');

                Route::apiResource('roles', RoleController::class)
                    ->only(['store', 'update'])
                    ->parameter('role', null);
            });

        /**
         * Category
         *
         * @route account.categories.*
         */
        Route::apiResource('categories', CategoryController::class)
            ->middleware([
                'permission:' . Permissions::CAN_MANAGE_ORGANIZATION_CATEGORIES,
            ])
            ->only(['show', 'update', 'store', 'destroy']);

        /**
         * Organization Settings
         *
         * @route account.settings.*
         */
        Route::prefix('settings')
            ->name('settings.')
            ->middleware([
                'permission:' . Permissions::CAN_MANAGE_ORGANIZATION_SETTINGS,
                'impersonating',
            ])
            ->group(function () {
                // account.settings.invoice.*
                Route::resource('invoice', InvoiceSettingsController::class)
                    ->only(['index', 'store']);

                // account.settings.tax.*
                Route::resource('tax', TaxSettingsController::class)
                    ->only(['index', 'store', 'update', 'destroy']);

                // account.settings.vat-identification-number.update
                Route::patch(
                    'vat-identification-number/update',
                    UpdateVatIdentificationNumberController::class,
                )->name('vat-identification-number.update');

                // account.settings.tax-number.update
                Route::patch(
                    'tax-number/update',
                    UpdateTaxNumberController::class,
                )->name('tax-number.update');

                // account.settings.media.auto_share.*
                Route::resource('media', OrganizationMediaSettingsController::class)
                    ->only(['index', 'update']);

                Route::get('offer-configuration', [OfferConfigurationController::class, 'index'])->name('offer-configuration.index');
                Route::patch('offer-configuration', [OfferConfigurationController::class, 'update'])->name('offer-configuration.update');

            });

        // account.address.*
        Route::apiResource('address', AccountAddressController::class)
            ->only(['store']);
    });

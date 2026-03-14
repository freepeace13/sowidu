<?php

use App\Enums\Permissions;
use Illuminate\Support\Facades\Route;
use Modules\Offer\Controllers\MyOffersController;
use Modules\Offer\Controllers\OfferController;
use Modules\Offer\Controllers\OfferItemController;
use Modules\Offer\Controllers\OfferManualItemController;
use Modules\Offer\Controllers\OfferPdfController;
use Modules\Offer\Controllers\OfferStatusController;
use Modules\Offer\Controllers\UpdateOfferMessagesController;
use Modules\Offer\Middleware\EnsureOfferParticipant;
use Modules\Offer\Middleware\OfferInertiaRequestHandler;

Route::middleware([
    'web',
])->group(function () {
    /**
     * Offers
     */
    Route::prefix('offers')
        ->group(function () {

            Route::middleware([
                'auth',
                'impersonating',
                OfferInertiaRequestHandler::class,
                'permission:' . Permissions::CAN_ACCESS_OFFERS,
            ])->group(function () {
                Route::controller(OfferController::class)
                    ->prefix('')
                    ->group(function () {
                        Route::get('', 'index')->name('offers.index');
                        Route::get('{offer}', 'show')
                            ->middleware(EnsureOfferParticipant::class)
                            ->name('offers.show');
                        Route::post('store', 'store')->name('offers.store');
                        Route::patch('{offer}/update', 'update')->name('offers.update');
                        Route::delete('{offer}/destroy', 'destroy')->name('offers.destroy');

                        Route::patch('{offer}/messages/update', UpdateOfferMessagesController::class)
                            ->name('offers.messages.update');
                    });

                Route::controller(OfferStatusController::class)
                    ->prefix('{offer}/status')
                    ->group(function () {
                        Route::post('send', 'send')->name('offers.status.send');
                        Route::post('reject', 'reject')->name('offers.status.reject');
                        Route::post('accept', 'accept')->name('offers.status.accept');
                        Route::post('cancel', 'cancel')->name('offers.status.cancel');
                    });

                Route::controller(OfferItemController::class)
                    ->prefix('{offer}/items')
                    ->group(function () {
                        Route::post('store', 'store')->name('offers.items.store');
                        Route::patch('{item}/update', 'update')->name('offers.items.update');
                        Route::delete('{item}/destroy', 'destroy')->name('offers.items.destroy');
                    });

                Route::controller(OfferManualItemController::class)
                    ->prefix('{offer}/manual-items')
                    ->group(function () {
                        Route::post('store', 'store')->name('offers.manual_items.store');
                        // Route::patch('{item}/update', 'update')->name('offers.manual_items.update');
                    });
            });

            // Public routes for offers
            Route::controller(OfferPdfController::class)
                ->prefix('{offer}/pdf')
                ->group(function () {
                    Route::get('stream', 'stream')
                        ->name('offers.pdf.stream');

                    Route::get('download', 'download')
                        ->name('offers.pdf.download');
                });
        });

    // Offer recipients routes
    Route::prefix('my-offers')
        ->middleware([
            'auth',
            OfferInertiaRequestHandler::class,
        ])
        ->group(function () {
            Route::controller(MyOffersController::class)
                ->group(function () {
                    Route::get('', 'index')->name('my-offers.index');

                    Route::middleware(EnsureOfferParticipant::class)
                        ->group(function () {
                            Route::get('{offer}', 'show')
                                ->name('my-offers.show');

                            Route::post('{offer}/accept', 'accept')
                                ->name('my-offers.accept');

                            Route::post('{offer}/reject', 'reject')
                                ->name('my-offers.reject');
                        });
                });
        });
});

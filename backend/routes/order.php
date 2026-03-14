<?php

use App\Http\Controllers\Inertia\Order\CreateIncomingOrderForForeignClientController;
use App\Http\Controllers\Inertia\Order\Files\OrderMediaController;
use App\Http\Controllers\Inertia\Order\Files\ShareOrderFileToOppositePartyController;
use App\Http\Controllers\Inertia\Order\IncomingOrderController;
use App\Http\Controllers\Inertia\Order\OrderDeliveryTicketController;
use App\Http\Controllers\Inertia\Order\OrderInvoiceController;
use App\Http\Controllers\Inertia\Order\OrderOverviewController;
use App\Http\Controllers\Inertia\Order\OrderProductController;
use App\Http\Controllers\Inertia\Order\OrderTimeLogController;
use App\Http\Controllers\Inertia\Order\OutgoingOrderController;
use App\Http\Controllers\Inertia\Order\ResponseAcceptController;
use App\Http\Controllers\Inertia\Order\ResponseDisapproveController;
use App\Http\Controllers\Inertia\Order\ResponseRejectController;
use App\Http\Controllers\Inertia\Order\ShareWorkLogToClientController;
use App\Http\Controllers\Inertia\Order\ShowOrderDetailsPageController;
use App\Http\Controllers\Inertia\Order\StartOrderTimeTrackController;
use App\Http\Controllers\Inertia\Order\StopOrderTimeTrackController;
use App\Http\Controllers\Inertia\Order\UpdateOrderController;
use App\Http\Controllers\Inertia\Order\WorkLogReportController;
use App\Http\Middleware\EnsureCanParticipateOnOrder;
use App\Http\Middleware\EnsureValidEmployee;
use App\Http\Middleware\Web\OrderHandleInertiaRequests;
use Illuminate\Support\Facades\Route;
use Modules\Offer\Controllers\OrderOfferController;

Route::name('orders.')
    ->prefix('orders')
    ->middleware([
        'auth',
        OrderHandleInertiaRequests::class,
    ])
    ->group(function () {
        Route::get('overview', OrderOverviewController::class)->name('overview');

        Route::apiResource('incoming', IncomingOrderController::class)
            ->parameter('incoming', 'order');

        Route::post('incoming/foreign', CreateIncomingOrderForForeignClientController::class)
            ->name('incoming.foreign_client.store');

        Route::apiResource('outgoing', OutgoingOrderController::class)
            ->parameter('outgoing', 'order');

        // orders.show
        Route::get('{order}', ShowOrderDetailsPageController::class)
            ->name('show')
            ->middleware(EnsureCanParticipateOnOrder::class);

        // orders.update
        Route::patch('{order}/update', UpdateOrderController::class)
            ->name('update')
            ->middleware([
                EnsureCanParticipateOnOrder::class,
                EnsureValidEmployee::class,
            ]);

        // orders.accept.response
        Route::patch('{order}/response/accept', ResponseAcceptController::class)
            ->middleware(EnsureCanParticipateOnOrder::class)
            ->name('accept.response');

        // orders.reject.response
        Route::delete('{order}/response/reject', ResponseRejectController::class)
            ->middleware(EnsureCanParticipateOnOrder::class)
            ->name('reject.response');

        // orders.disapprove.response
        Route::patch('{order}/response/disapprove', ResponseDisapproveController::class)
            ->middleware(EnsureCanParticipateOnOrder::class)
            ->name('disapprove.response');

        Route::prefix('{order}/time-tracker')
            ->middleware(EnsureCanParticipateOnOrder::class)
            ->name('time_tracker.')
            ->group(function () {
                // orders.time_tracker.start
                Route::patch('start', StartOrderTimeTrackController::class)
                    ->middleware(EnsureCanParticipateOnOrder::class)
                    ->name('start');

                // orders.time_tracker.stop
                Route::patch('{workLog}/stop', StopOrderTimeTrackController::class)
                    ->middleware(EnsureCanParticipateOnOrder::class)
                    ->name('stop');

                // orders.time_tracker.report
                Route::apiResource('{workLog}/report', WorkLogReportController::class)
                    ->only(['store', 'update', 'destroy']);

                Route::apiResource('{workLog}/report', WorkLogReportController::class)
                    ->only(['store', 'update', 'destroy']);
            });

        Route::prefix('{order}/work-logs/{workLog}')
            ->middleware(EnsureCanParticipateOnOrder::class)
            ->group(function () {
                // orders.work_logs.share
                Route::patch('share', ShareWorkLogToClientController::class)
                    ->middleware(EnsureCanParticipateOnOrder::class)
                    ->name('work_logs.share');
            });

        // orders.show.files.medias.*
        Route::prefix('{order}/files/medias')
            ->middleware(EnsureCanParticipateOnOrder::class)
            ->name('show.files.medias.')
            ->controller(OrderMediaController::class)
            ->group(function () {
                Route::get('', 'index')->name('index');
                Route::post('', 'store')->name('store');
                Route::delete('{media}/destroy', 'destroy')->name('destroy');
                Route::delete('{media}/detach', 'detach')->name('detach');
            });

        // orders.show.products.*
        Route::prefix('{order}/products')
            ->middleware(EnsureCanParticipateOnOrder::class)
            ->name('show.products.')
            ->controller(OrderProductController::class)
            ->group(function () {
                Route::get('', 'index')->name('index');
                Route::post('', 'store')->name('store');
                Route::patch('{orderProduct}/update', 'update')->name('update');
                Route::delete('{orderProduct}/destroy', 'destroy')->name('destroy');
            });

        // orders.show.invoices.*
        Route::prefix('{order}/invoices')
            ->middleware(EnsureCanParticipateOnOrder::class)
            ->controller(OrderInvoiceController::class)
            ->group(function () {
                Route::get('', 'index')->name('show.invoices.index');
                Route::get('{invoice}', 'show')->name('show.invoices.show');
            });

        // orders.show.invoices.*
        Route::prefix('{order}/delivery-tickets')
            ->middleware(EnsureCanParticipateOnOrder::class)
            ->controller(OrderDeliveryTicketController::class)
            ->group(function () {
                Route::get('', 'index')->name('show.delivery_tickets.index');
                Route::get('{deliveryTicket}', 'show')->name('show.delivery_tickets.show');
                Route::post('', 'import')->name('show.delivery_tickets.import');
                Route::put('{deliveryTicket}', 'update')->name('show.delivery_tickets.update');
            });

        // TODO: Uncomment, removed other media linking except for `Media`
        // orders.show.files.documents.*
        // Route::prefix('{order}/files/documents')
        //     ->middleware(EnsureCanParticipateOnOrder::class)
        //     ->name('show.files.documents.')
        //     ->controller(OrderDocumentController::class)
        //     ->group(function () {
        //         Route::get('', 'index')->name('index');
        //         Route::post('', 'store')->name('store');
        //         Route::delete('{media}/destroy', 'destroy')->name('destroy');
        //         Route::delete('{media}/detach', 'detach')->name('detach');
        //     });

        // orders.show.time_logs.*
        Route::prefix('{order}/time-logs')
            ->middleware(EnsureCanParticipateOnOrder::class)
            ->controller(OrderTimeLogController::class)
            ->group(
                function () {
                    Route::get('', 'index')->name('show.time_logs.index');
                    Route::post('store', 'store')->name('show.time_logs.store');
                    Route::patch('{workLog}/update', 'update')->name('show.time_logs.update');
                },
            );

        // orders.orders.files.share_to_opposite_party
        Route::post('{order}/files/{media}/share-to-opposite-party', ShareOrderFileToOppositePartyController::class)
            ->name('orders.files.share_to_opposite_party')
            ->middleware(EnsureCanParticipateOnOrder::class);

        /**
         * Order Offers
         */
        // orders.show.offers.*
        Route::prefix('{order}/offers')
            ->middleware([
                EnsureCanParticipateOnOrder::class,
            ])
            ->group(function () {
                Route::controller(OrderOfferController::class)
                    ->group(function () {
                        Route::get('', 'index')->name('offers.index');
                        Route::get('{offer}', 'show')->name('offers.show');
                    });
            });
    });

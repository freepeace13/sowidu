<?php

namespace Modules\Offer;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class OfferEventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        \Modules\Offer\Events\OfferSent::class => [
            \Modules\Offer\Listeners\SendOfferEmail::class,
            \Modules\Offer\Listeners\NotifyRecipientAboutOffer::class,
        ],
        \Modules\Offer\Events\OfferItemsUpdated::class => [
            \Modules\Offer\Listeners\RecalculateOfferTotals::class,
        ],
        \Modules\Offer\Events\OfferAccepted::class => [
            \Modules\Offer\Listeners\CreateOrderFromOffer::class,
            \Modules\Offer\Listeners\SendOfferAcceptedNotification::class,
            \Modules\Offer\Listeners\LogOfferStatusChanged::class,
        ],
        \Modules\Offer\Events\OfferRejected::class => [
            \Modules\Offer\Listeners\SendOfferRejectedNotification::class,
            \Modules\Offer\Listeners\LogOfferStatusChanged::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // You can register any manual events here if needed
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }

    /**
     * Get the listener directories that should be used to discover events.
     *
     * @return array<int, string>
     */
    protected function discoverEventsWithin(): array
    {
        return [
            base_path('modules/offer/src/Listeners'),
        ];
    }
}

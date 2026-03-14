<?php

namespace Modules\DeliveryTicket\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketMaterial;

class DeliveryTicketServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/delivery-ticket.php', 'deliveryticket');
        $this->app->register(PolicyServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(BindingServiceProvider::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'deliveryticket');
        $this->bootRelations();
    }

    protected function bootRelations()
    {
        Relation::morphMap([
            'delivery_tickets' => DeliveryTicket::class,
            'delivery_ticket_materials' => DeliveryTicketMaterial::class,
        ]);
    }
}

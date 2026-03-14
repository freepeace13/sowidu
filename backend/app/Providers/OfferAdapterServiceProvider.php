<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Offer\AddressbookServiceAdapter;
use App\Services\Offer\CacheServiceAdapter;
use App\Services\Offer\CatalogServiceAdapter;
use App\Services\Offer\CompanyServiceAdapter;
use App\Services\Offer\EmployeeServiceAdapter;
use App\Services\Offer\InvoiceServiceAdapter;
use App\Services\Offer\NotificationServiceAdapter;
use App\Services\Offer\OrderServiceAdapter;
use App\Services\Offer\PlaceServiceAdapter;
use App\Services\Offer\UserServiceAdapter;
use Illuminate\Support\ServiceProvider;
use Modules\Offer\Contracts\External\AddressbookServiceContract;
use Modules\Offer\Contracts\External\CacheServiceContract;
use Modules\Offer\Contracts\External\CatalogServiceContract;
use Modules\Offer\Contracts\External\CompanyServiceContract;
use Modules\Offer\Contracts\External\EmployeeServiceContract;
use Modules\Offer\Contracts\External\InvoiceServiceContract;
use Modules\Offer\Contracts\External\NotificationServiceContract;
use Modules\Offer\Contracts\External\OrderServiceContract;
use Modules\Offer\Contracts\External\PlaceServiceContract;
use Modules\Offer\Contracts\External\UserServiceContract;

/**
 * Service provider for binding Offer module external contracts to adapters.
 *
 * This provider binds the interfaces defined in the Offer module to
 * concrete implementations in the main application.
 */
class OfferAdapterServiceProvider extends ServiceProvider
{
    /**
     * All contract to adapter bindings.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        UserServiceContract::class => UserServiceAdapter::class,
        CompanyServiceContract::class => CompanyServiceAdapter::class,
        EmployeeServiceContract::class => EmployeeServiceAdapter::class,
        AddressbookServiceContract::class => AddressbookServiceAdapter::class,
        CatalogServiceContract::class => CatalogServiceAdapter::class,
        OrderServiceContract::class => OrderServiceAdapter::class,
        InvoiceServiceContract::class => InvoiceServiceAdapter::class,
        PlaceServiceContract::class => PlaceServiceAdapter::class,
        NotificationServiceContract::class => NotificationServiceAdapter::class,
        CacheServiceContract::class => CacheServiceAdapter::class,
    ];

    public function register(): void
    {
        // Bindings are automatically registered via $bindings property
    }
}

<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\DeliveryTicket\AuthorizationAdapter;
use App\Services\DeliveryTicket\CacheAdapter;
use App\Services\DeliveryTicket\CatalogAdapter;
use App\Services\DeliveryTicket\CompanyAdapter;
use App\Services\DeliveryTicket\ImpersonatorAdapter;
use App\Services\DeliveryTicket\InertiaAdapter;
use App\Services\DeliveryTicket\InvoiceAdapter;
use App\Services\DeliveryTicket\MediaAdapter;
use App\Services\DeliveryTicket\ModelConfigAdapter;
use App\Services\DeliveryTicket\TransformerAdapter;
use App\Services\DeliveryTicket\ValidationAdapter;
use App\Services\DeliveryTicket\VuetifyAdapter;
use Illuminate\Support\ServiceProvider;
use Modules\DeliveryTicket\Contracts\External\AuthorizationContract;
use Modules\DeliveryTicket\Contracts\External\CacheContract;
use Modules\DeliveryTicket\Contracts\External\CatalogContract;
use Modules\DeliveryTicket\Contracts\External\CompanyContract;
use Modules\DeliveryTicket\Contracts\External\ImpersonatorContract;
use Modules\DeliveryTicket\Contracts\External\InertiaContract;
use Modules\DeliveryTicket\Contracts\External\InvoiceContract;
use Modules\DeliveryTicket\Contracts\External\MediaContract;
use Modules\DeliveryTicket\Contracts\External\ModelConfigContract;
use Modules\DeliveryTicket\Contracts\External\TransformerContract;
use Modules\DeliveryTicket\Contracts\External\ValidationContract;
use Modules\DeliveryTicket\Contracts\External\VuetifyContract;

/**
 * Service provider for DeliveryTicket module external contract bindings.
 *
 * This provider binds the module's external contracts to their adapter implementations.
 */
class DeliveryTicketServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ModelConfigContract::class, ModelConfigAdapter::class);
        $this->app->bind(TransformerContract::class, TransformerAdapter::class);
        $this->app->bind(CatalogContract::class, CatalogAdapter::class);
        $this->app->bind(MediaContract::class, MediaAdapter::class);
        $this->app->bind(CompanyContract::class, CompanyAdapter::class);
        $this->app->bind(AuthorizationContract::class, AuthorizationAdapter::class);
        $this->app->bind(ImpersonatorContract::class, ImpersonatorAdapter::class);
        $this->app->bind(CacheContract::class, CacheAdapter::class);
        $this->app->bind(InvoiceContract::class, InvoiceAdapter::class);
        $this->app->bind(VuetifyContract::class, VuetifyAdapter::class);
        $this->app->bind(ValidationContract::class, ValidationAdapter::class);
        $this->app->bind(InertiaContract::class, InertiaAdapter::class);
    }
}

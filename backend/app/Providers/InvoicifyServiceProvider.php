<?php

namespace App\Providers;

use App\Services\Invoicify\CompanyServiceAdapter;
use Illuminate\Support\ServiceProvider;
use Modules\Invoicify\Contracts\External\CompanyServiceContract;

/**
 * Service Provider for Invoicify module external dependencies.
 *
 * This provider binds all external contracts (outgoing ports) to their
 * concrete adapter implementations, following the Ports and Adapters
 * (Hexagonal Architecture) pattern.
 */
class InvoicifyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CompanyServiceContract::class, CompanyServiceAdapter::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

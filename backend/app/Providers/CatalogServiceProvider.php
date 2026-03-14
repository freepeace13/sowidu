<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CatalogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Modules\Catalog\Contracts\External\MediaManagerContract::class,
            \App\Services\Catalog\MediaManagerAdapter::class,
        );

        $this->app->bind(
            \Modules\Catalog\Contracts\External\PermissionContract::class,
            \App\Services\Catalog\PermissionAdapter::class,
        );

        $this->app->bind(
            \Modules\Catalog\Contracts\External\CompanyInfoContract::class,
            \App\Services\Catalog\CompanyInfoAdapter::class,
        );

        $this->app->bind(
            \Modules\Catalog\Contracts\External\PermissionContract::class,
            \App\Services\Catalog\PermissionAdapter::class,
        );
    }

    public function boot(): void
    {
        //
    }
}

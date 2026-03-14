<?php

namespace Modules\Catalog;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Catalog\Actions\CreateCatalogItem as CreateCatalogItemAction;
use Modules\Catalog\Actions\CreateCatalogItemType as CreateCatalogItemTypeAction;
use Modules\Catalog\Actions\DeleteCatalogItem as DeleteCatalogItemAction;
use Modules\Catalog\Actions\UpdateCatalogItem as UpdateCatalogItemAction;
use Modules\Catalog\Contracts\CreatesCatalogItem as CreatesCatalogItemContract;
use Modules\Catalog\Contracts\CreatesCatalogItemType as CreatesCatalogItemTypeContract;
use Modules\Catalog\Contracts\DeletesCatalogItem as DeletesCatalogItemContract;
use Modules\Catalog\Contracts\UpdatesCatalogItem as UpdatesCatalogItemContract;

class CatalogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/catalog.php', 'catalog');

        $this->app->bind(CreatesCatalogItemContract::class, CreateCatalogItemAction::class);
        $this->app->bind(CreatesCatalogItemTypeContract::class, CreateCatalogItemTypeAction::class);
        $this->app->bind(DeletesCatalogItemContract::class, DeleteCatalogItemAction::class);
        $this->app->bind(UpdatesCatalogItemContract::class, UpdateCatalogItemAction::class);
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerPolicies();

        if ($this->app->runningInConsole()) {
            // $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => config('catalog.prefix'),
            'middleware' => config('catalog.middleware'),
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    protected function registerPolicies(): void
    {
        Gate::policy(
            \Modules\Catalog\Models\CatalogItem::class,
            \Modules\Catalog\Policies\CatalogItemPolicy::class,
        );
    }
}

<?php

namespace Modules\Offer;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Modules\Offer\Console\AttachDefaultTaxToOffersCommand;
use Modules\Offer\Console\MigrateOrderRelationToOfferCommand;
use Modules\Offer\Console\SeedUnitNamesOnOffersCommand;
use Modules\Offer\Models\Offer;
use Modules\Offer\Policies\OfferPolicy;
use Modules\Offer\Support\Pdf\DefaultPathGenerator;
use Modules\Offer\Support\Pdf\MpdfFactory;
use Modules\Offer\Support\Pdf\PathGenerator;

class OfferServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'offer');

        // Register anonymous components with 'offer' namespace
        Blade::anonymousComponentPath(__DIR__ . '/../resources/views/components', 'offer');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        Inertia::setRootView('offer::app');

        Gate::policy(Offer::class, OfferPolicy::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                AttachDefaultTaxToOffersCommand::class,
                MigrateOrderRelationToOfferCommand::class,
                SeedUnitNamesOnOffersCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/offer.php' => config_path('offer.php'),
            ], 'config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/offer.php',
            'offer',
        );

        $this->app->bind('offer.mpdf', function ($app) {
            return new MpdfFactory;
        });

        $this->app->bind(PathGenerator::class, DefaultPathGenerator::class);
    }
}

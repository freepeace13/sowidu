<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class WorkLogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/worklog.php', 'worklog');

        $this->app->register(BindingServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(PolicyServiceProvider::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'worklogs');

        $this->app->booted(function () {
            Inertia::setRootView('worklogs::app');
        });
    }
}

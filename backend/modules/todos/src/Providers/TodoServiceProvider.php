<?php

namespace Modules\Todos\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class TodoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/todo.php', 'todos');

        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'todos');

        $this->app->booted(function () {
            Inertia::setRootView('todos::app');
        });
    }
}

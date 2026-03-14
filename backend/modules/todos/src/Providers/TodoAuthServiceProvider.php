<?php

namespace Modules\Todos\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class TodoAuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \Modules\Todos\Models\Board::class => \Modules\Todos\Policies\BoardPolicy::class,
        \Modules\Todos\Models\Task::class => \Modules\Todos\Policies\TaskPolicy::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register() {}

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}

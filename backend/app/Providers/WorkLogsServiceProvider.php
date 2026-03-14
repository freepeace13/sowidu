<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class WorkLogsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Modules\WorkLogs\Contracts\External\AuthorizationContract::class,
            \App\Services\WorkLogs\AuthorizationAdapter::class,
        );

        $this->app->bind(
            \Modules\WorkLogs\Contracts\External\ActivityLogReportContract::class,
            \App\Services\WorkLogs\ActivityLogReportAdapter::class,
        );

        $this->app->bind(
            \Modules\WorkLogs\Contracts\External\ImpersonatorContract::class,
            \App\Services\WorkLogs\ImpersonatorAdapter::class,
        );

        $this->app->bind(
            \Modules\WorkLogs\Contracts\External\EmployeeContract::class,
            \App\Services\WorkLogs\EmployeeAdapter::class,
        );

        $this->app->bind(
            \Modules\WorkLogs\Contracts\External\TransformerContract::class,
            \App\Services\WorkLogs\TransformerAdapter::class,
        );

        $this->app->bind(
            \Modules\WorkLogs\Contracts\External\ModelConfigContract::class,
            \App\Services\WorkLogs\ModelConfigAdapter::class,
        );

        $this->app->bind(
            \Modules\WorkLogs\Contracts\External\PolicyAuthorizationContract::class,
            \App\Services\WorkLogs\PolicyAuthorizationAdapter::class,
        );

        $this->app->bind(
            \Modules\WorkLogs\Contracts\External\InertiaMiddlewareContract::class,
            \App\Services\WorkLogs\InertiaMiddlewareAdapter::class,
        );
    }

    public function boot(): void
    {
        //
    }
}

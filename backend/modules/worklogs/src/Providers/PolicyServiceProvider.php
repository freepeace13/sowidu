<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\WorkLogs\Models\WorkLog;
use Modules\WorkLogs\Policies\WorkLogPolicy;

class PolicyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(WorkLog::class, WorkLogPolicy::class);
    }
}

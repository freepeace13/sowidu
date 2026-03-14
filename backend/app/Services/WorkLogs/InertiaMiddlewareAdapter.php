<?php

declare(strict_types=1);

namespace App\Services\WorkLogs;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;
use Modules\WorkLogs\Contracts\External\InertiaMiddlewareContract;

class InertiaMiddlewareAdapter implements InertiaMiddlewareContract
{
    protected HandleInertiaRequests $middleware;

    public function __construct()
    {
        $this->middleware = new HandleInertiaRequests;
    }

    public function getSharedData(Request $request, array $extraTranslations = [], array $permissions = []): array
    {
        // Set extra translations and permissions on the middleware
        $this->middleware->extraTranslations = $extraTranslations;
        $this->middleware->permissions = $permissions;

        return $this->middleware->share($request);
    }

    public function getVersion(Request $request): ?string
    {
        return $this->middleware->version($request);
    }
}

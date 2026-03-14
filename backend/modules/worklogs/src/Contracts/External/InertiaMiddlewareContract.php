<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Contracts\External;

use Illuminate\Http\Request;

/**
 * Outgoing port for Inertia middleware functionality.
 *
 * Provides shared data and configuration for Inertia middleware
 * without extending main app middleware directly.
 */
interface InertiaMiddlewareContract
{
    /**
     * Get the base shared data for Inertia.
     *
     * @param  Request  $request  The HTTP request
     * @param  array  $extraTranslations  Additional translation keys
     * @param  array  $permissions  Additional permissions to check
     * @return array The shared data
     */
    public function getSharedData(Request $request, array $extraTranslations = [], array $permissions = []): array;

    /**
     * Get the asset version.
     */
    public function getVersion(Request $request): ?string;
}

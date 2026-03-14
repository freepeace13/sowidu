<?php

namespace Modules\Offer\Contracts\External;

use Illuminate\Database\Eloquent\Model;

/**
 * Outgoing port for cache-related services needed by the Offer module.
 */
interface CacheServiceContract
{
    /**
     * Get a value from cache.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Put a value in cache.
     */
    public function put(string $key, mixed $value, int $ttl = 3600): void;

    /**
     * Forget a cached value.
     */
    public function forget(string $key): void;

    /**
     * Remember a value in cache.
     */
    public function remember(string $key, int $ttl, callable $callback): mixed;

    /**
     * Clear cache for a specific tag or pattern.
     */
    public function clearByTag(string $tag): void;

    /**
     * Get the currency for a company.
     */
    public function getCompanyCurrency(Model $company): string;
}

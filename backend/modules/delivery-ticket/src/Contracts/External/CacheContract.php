<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Contracts\External;

/**
 * Contract for caching operations.
 *
 * Provides cache functionality for delivery ticket data.
 */
interface CacheContract
{
    /**
     * Get cached value or execute callback.
     */
    public function remember(string $key, int $ttl, callable $callback): mixed;

    /**
     * Forget a cached value.
     */
    public function forget(string $key): void;

    /**
     * Flush cache by tag/prefix.
     */
    public function flush(string $tag): void;

    /**
     * Get company currency from cache.
     */
    public function getCompanyCurrency(mixed $company): string;
}

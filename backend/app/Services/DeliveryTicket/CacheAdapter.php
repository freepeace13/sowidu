<?php

namespace App\Services\DeliveryTicket;

use App\Services\CacheService;
use Modules\DeliveryTicket\Contracts\External\CacheContract;

class CacheAdapter implements CacheContract
{
    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        return CacheService::remember($key, $ttl, $callback);
    }

    public function forget(string $key): void
    {
        CacheService::forget($key);
    }

    public function flush(string $tag): void
    {
        CacheService::flush($tag);
    }

    public function getCompanyCurrency(mixed $company): string
    {
        return CacheService::getCompanyCurrency($company);
    }
}

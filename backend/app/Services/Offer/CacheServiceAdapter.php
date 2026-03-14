<?php

declare(strict_types=1);

namespace App\Services\Offer;

use App\Models\Company;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Model;
use Modules\Offer\Contracts\External\CacheServiceContract;

class CacheServiceAdapter implements CacheServiceContract
{
    public function __construct(
        protected CacheService $cacheService,
    ) {}

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->cacheService->get($key, $default);
    }

    public function put(string $key, mixed $value, int $ttl = 3600): void
    {
        $this->cacheService->put($key, $value, $ttl);
    }

    public function forget(string $key): void
    {
        $this->cacheService->forget($key);
    }

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        return $this->cacheService->remember($key, $ttl, $callback);
    }

    public function clearByTag(string $tag): void
    {
        $this->cacheService->clearByTag($tag);
    }

    public function getCompanyCurrency(Model $company): string
    {
        /** @var Company $company */
        return CacheService::getCompanyCurrency($company);
    }
}

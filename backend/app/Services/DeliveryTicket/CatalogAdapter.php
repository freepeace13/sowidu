<?php

namespace App\Services\DeliveryTicket;

use App\Models\CatalogItem;
use App\Services\CatalogService;
use Modules\DeliveryTicket\Contracts\External\CatalogContract;

class CatalogAdapter implements CatalogContract
{
    public function getItems(mixed $user, mixed $company): mixed
    {
        return CatalogService::make($user, $company);
    }

    public function findItem(int $id): mixed
    {
        return CatalogItem::findOrFail($id);
    }

    public function findItemWithRelations(int $id, array $relations = []): mixed
    {
        return CatalogItem::with($relations)->findOrFail($id);
    }

    public function getUnits(mixed $company): mixed
    {
        return $company->catalogItemUnits ?? collect();
    }

    public function getAllItemTypes(mixed $user, mixed $company): array
    {
        return CatalogService::make($user, $company)->allItemTypes()->toArray();
    }
}

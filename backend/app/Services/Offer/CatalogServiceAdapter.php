<?php

declare(strict_types=1);

namespace App\Services\Offer;

use App\Models\CatalogItem;
use App\Models\CatalogItemUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Offer\Contracts\External\CatalogServiceContract;

class CatalogServiceAdapter implements CatalogServiceContract
{
    public function findItem(int $id): ?Model
    {
        return CatalogItem::find($id);
    }

    public function findItemOrFail(int $id): Model
    {
        return CatalogItem::findOrFail($id);
    }

    public function findUnit(int $id): ?Model
    {
        return CatalogItemUnit::find($id);
    }

    public function findUnitOrFail(int $id): Model
    {
        return CatalogItemUnit::findOrFail($id);
    }

    public function getUnitsByCompany(int $companyId): Collection
    {
        return CatalogItemUnit::where('company_id', $companyId)->get();
    }

    public function getUnitName(Model $unit): string
    {
        /** @var CatalogItemUnit $unit */
        return $unit->name ?? '';
    }

    public function getAllUnitsForOptions(): Collection
    {
        return CatalogItemUnit::all();
    }
}

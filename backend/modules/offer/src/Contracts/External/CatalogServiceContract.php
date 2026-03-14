<?php

namespace Modules\Offer\Contracts\External;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Outgoing port for catalog-related services needed by the Offer module.
 * Provides access to CatalogItem and CatalogItemUnit.
 */
interface CatalogServiceContract
{
    /**
     * Find a catalog item by ID.
     */
    public function findItem(int $id): ?Model;

    /**
     * Find a catalog item by ID or fail.
     */
    public function findItemOrFail(int $id): Model;

    /**
     * Find a catalog item unit by ID.
     */
    public function findUnit(int $id): ?Model;

    /**
     * Find a catalog item unit by ID or fail.
     */
    public function findUnitOrFail(int $id): Model;

    /**
     * Get all units for a company.
     */
    public function getUnitsByCompany(int $companyId): Collection;

    /**
     * Get the unit name.
     */
    public function getUnitName(Model $unit): string;

    /**
     * Get all units for options dropdown.
     */
    public function getAllUnitsForOptions(): Collection;
}

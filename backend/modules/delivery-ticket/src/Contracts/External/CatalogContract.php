<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Contracts\External;

/**
 * Contract for catalog operations.
 *
 * Provides access to catalog items and units.
 */
interface CatalogContract
{
    /**
     * Get catalog items for the given company.
     */
    public function getItems(mixed $user, mixed $company): mixed;

    /**
     * Find a catalog item by ID.
     */
    public function findItem(int $id): mixed;

    /**
     * Find a catalog item with relations.
     */
    public function findItemWithRelations(int $id, array $relations = []): mixed;

    /**
     * Get catalog item units.
     */
    public function getUnits(mixed $company): mixed;

    /**
     * Get all item types for the given user and company.
     */
    public function getAllItemTypes(mixed $user, mixed $company): array;
}

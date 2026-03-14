<?php

namespace Modules\Offer\Contracts\External;

use Illuminate\Database\Eloquent\Model;

/**
 * Outgoing port for place-related services needed by the Offer module.
 */
interface PlaceServiceContract
{
    /**
     * Find a place by ID.
     */
    public function find(int $id): ?Model;

    /**
     * Find a place by ID or fail.
     */
    public function findOrFail(int $id): Model;

    /**
     * Transform a place for API/frontend consumption.
     */
    public function transform(Model $place): array;

    /**
     * Transform a place for offer display (id, google map url, short full address).
     */
    public function transformForOffer(?Model $place): ?array;
}

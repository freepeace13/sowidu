<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Contracts\External;

/**
 * Outgoing port for transformer operations.
 *
 * The main application provides transformer adapters for
 * User, Place, and ActivityLogReport transformations.
 */
interface TransformerContract
{
    /**
     * Transform a user to array format.
     *
     * @param  mixed  $user  The user to transform
     * @return array Transformed user data
     */
    public function transformUser(mixed $user): array;

    /**
     * Transform a place/address to array format.
     *
     * @param  mixed  $place  The place to transform
     * @return array|null Transformed place data with id
     */
    public function transformPlaceWithId(mixed $place): ?array;

    /**
     * Transform an activity log report to array format.
     *
     * @param  mixed  $report  The report to transform
     * @return array Transformed report data
     */
    public function transformActivityLogReport(mixed $report): array;
}

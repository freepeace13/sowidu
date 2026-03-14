<?php

namespace Modules\Offer\Contracts\External;

use Illuminate\Database\Eloquent\Model;

/**
 * Outgoing port for user-related services needed by the Offer module.
 */
interface UserServiceContract
{
    /**
     * Find a user by ID.
     */
    public function find(int $id): ?Model;

    /**
     * Find a user by ID or fail.
     */
    public function findOrFail(int $id): Model;

    /**
     * Transform user for recipient details (name, email, photo, address, phone).
     */
    public function transformForRecipient(Model $user): array;

    /**
     * Get user avatar URL.
     */
    public function getAvatarUrl(Model $user): ?string;
}

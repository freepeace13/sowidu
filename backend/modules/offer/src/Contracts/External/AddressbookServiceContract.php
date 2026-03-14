<?php

namespace Modules\Offer\Contracts\External;

use Illuminate\Database\Eloquent\Model;

/**
 * Outgoing port for addressbook-related services needed by the Offer module.
 * Replaces direct usage of AddressbookHelper.
 */
interface AddressbookServiceContract
{
    /**
     * Find an addressbook entry by ID.
     */
    public function find(int $id): ?Model;

    /**
     * Find an addressbook entry by ID or fail.
     */
    public function findOrFail(int $id): Model;

    /**
     * Get the display name for an addressbook entry.
     */
    public function getDisplayName(Model $addressbook): string;

    /**
     * Get the email for an addressbook entry.
     */
    public function getEmail(Model $addressbook): ?string;

    /**
     * Get the primary contact email for an addressbook entry.
     */
    public function getPrimaryContactEmail(Model $addressbook): ?string;

    /**
     * Check if addressbook entry has a valid email.
     */
    public function hasEmail(Model $addressbook): bool;

    /**
     * Find a user by email address.
     */
    public function findUserByEmail(string $email): ?Model;

    /**
     * Get addressbook IDs associated with a user.
     */
    public function getAddressbookIdsFromUser(Model $user): \Illuminate\Support\Collection;

    /**
     * Transform addressbook with address for API/frontend consumption.
     */
    public function transformWithAddress(Model $addressbook): array;

    /**
     * Check if addressbook is a foreign organization.
     */
    public function isForeignOrganization(Model $addressbook): bool;
}

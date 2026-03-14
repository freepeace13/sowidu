<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Contracts\External;

/**
 * Contract for authorization operations.
 *
 * Provides team/company authorization functionality.
 */
interface AuthorizationContract
{
    /**
     * Check if user has access to the given company.
     */
    public function hasCompanyAccess(mixed $user, mixed $company): bool;

    /**
     * Check if user can perform action on model.
     */
    public function can(mixed $user, string $ability, mixed $model): bool;

    /**
     * Get the user's current company.
     */
    public function getCurrentCompany(mixed $user): mixed;
}

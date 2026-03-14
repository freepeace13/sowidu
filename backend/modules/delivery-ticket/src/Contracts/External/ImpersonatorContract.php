<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Contracts\External;

/**
 * Contract for impersonation operations.
 *
 * Provides impersonation context for viewing data as another user.
 */
interface ImpersonatorContract
{
    /**
     * Check if currently impersonating.
     */
    public function isImpersonating(): bool;

    /**
     * Get the impersonated user.
     */
    public function getImpersonatedUser(): mixed;

    /**
     * Get the original user (impersonator).
     */
    public function getOriginalUser(): mixed;
}

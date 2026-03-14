<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Contracts\External;

/**
 * Outgoing port for impersonation functionality.
 *
 * The main application provides the impersonation session adapter.
 */
interface ImpersonatorContract
{
    /**
     * Check if user is currently impersonating.
     */
    public function isImpersonating(): bool;

    /**
     * Get the current team/company ID from impersonation context.
     */
    public function getTeamId(): ?int;

    /**
     * Get the current impersonated user.
     */
    public function getUser(): mixed;

    /**
     * Get the current impersonated company.
     */
    public function getCompany(): mixed;
}

<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Contracts\External;

/**
 * Outgoing port for policy authorization.
 *
 * Provides authorization methods for policies without
 * importing main app traits directly.
 */
interface PolicyAuthorizationContract
{
    /**
     * Check if currently impersonating a company.
     */
    public function isImpersonating(): bool;

    /**
     * Get the current team/company ID.
     */
    public function getCurrentTeamId(): ?int;

    /**
     * Get the current employee for the authenticated user.
     *
     * @return mixed The employee model
     */
    public function getCurrentEmployee(): mixed;

    /**
     * Check if user can represent a team with given permission.
     *
     * @param  mixed  $user  The user
     * @param  int|null  $teamId  The team ID
     * @param  string  $permission  The permission to check
     */
    public function canRepresentTeam(mixed $user, ?int $teamId, string $permission): bool;

    /**
     * Check if user is authorized for a permission on a team.
     *
     * @param  mixed  $user  The user
     * @param  string  $permission  The permission to check
     * @param  int|null  $teamId  Optional team ID (uses current if null)
     */
    public function isAuthorizedTo(mixed $user, string $permission, ?int $teamId = null): bool;
}

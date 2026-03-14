<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Contracts\External;

/**
 * Outgoing port for authorization and permissions.
 *
 * The main application provides Laravel Gate/Policy adapters.
 */
interface AuthorizationContract
{
    /**
     * Check if a user can perform an action on a resource.
     *
     * @param  mixed  $user  The user performing the action
     * @param  string  $ability  The ability/action
     * @param  mixed  $resource  The resource being acted upon
     */
    public function can(mixed $user, string $ability, mixed $resource = null): bool;

    /**
     * Authorize an action or throw an exception.
     *
     * @param  mixed  $user  The user performing the action
     * @param  string  $ability  The ability/action
     * @param  mixed  $resource  The resource being acted upon
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize(mixed $user, string $ability, mixed $resource = null): void;

    /**
     * Check if user has a specific permission.
     *
     * @param  mixed  $user  The user
     * @param  string  $permission  Permission name
     */
    public function hasPermission(mixed $user, string $permission): bool;

    /**
     * Check if user is currently impersonating.
     */
    public function isImpersonating(): bool;

    /**
     * Get the current team ID from impersonation context.
     */
    public function getCurrentTeamId(): ?int;
}

<?php

namespace App\Services\Chat;

use Illuminate\Support\Facades\Gate;
use Modules\Chatly\Contracts\External\AuthorizationContract;

/**
 * Adapter for authorization and permissions.
 *
 * Wraps Laravel's Gate and permission system to provide the interface
 * required by the Chatly module.
 */
class AuthorizationAdapter implements AuthorizationContract
{
    /**
     * Check if a user can perform an action on a resource.
     *
     * @param  mixed  $user  The user performing the action
     * @param  string  $ability  The ability/action (e.g., 'sendMessage', 'addParticipants')
     * @param  mixed  $resource  The resource being acted upon
     */
    public function can(mixed $user, string $ability, mixed $resource = null): bool
    {
        if ($resource === null) {
            return Gate::forUser($user)->allows($ability);
        }

        return Gate::forUser($user)->allows($ability, $resource);
    }

    /**
     * Authorize an action or throw an exception.
     *
     * @param  mixed  $user  The user performing the action
     * @param  string  $ability  The ability/action
     * @param  mixed  $resource  The resource being acted upon
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize(mixed $user, string $ability, mixed $resource = null): void
    {
        if ($resource === null) {
            Gate::forUser($user)->authorize($ability);

            return;
        }

        // Handle array parameters (e.g., [$conversation, $participation])
        if (is_array($resource)) {
            Gate::forUser($user)->authorize($ability, $resource);

            return;
        }

        Gate::forUser($user)->authorize($ability, $resource);
    }

    /**
     * Check if user has a specific permission.
     *
     * @param  string  $permission  Permission name (e.g., 'CAN_ACCESS_CHAT')
     */
    public function hasPermission(mixed $user, string $permission): bool
    {
        // Check if user has the hasPermissionTo method (from Spatie Permission package)
        if (method_exists($user, 'hasPermissionTo')) {
            return $user->hasPermissionTo($permission);
        }

        // Fallback to checking permissions relation
        if (method_exists($user, 'permissions')) {
            return $user->permissions->contains('name', $permission);
        }

        return false;
    }
}

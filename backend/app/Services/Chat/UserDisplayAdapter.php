<?php

namespace App\Services\Chat;

use Modules\Chatly\Contracts\External\UserDisplayContract;

/**
 * Adapter for user display information (avatars, names, etc.).
 *
 * Wraps helper functions and methods to provide the interface
 * required by the Chatly module.
 */
class UserDisplayAdapter implements UserDisplayContract
{
    /**
     * Get avatar URL for a user or team member.
     *
     * @param  mixed  $user  User or Employee model
     * @return string Avatar URL
     */
    public function getAvatarUrl(mixed $user): string
    {
        return get_user_avatar_url($user);
    }

    /**
     * Get avatar URL for a company/organization.
     *
     * @param  mixed  $company  Company model
     * @return string Avatar URL
     */
    public function getCompanyAvatarUrl(mixed $company): string
    {
        return get_company_avatar_url($company);
    }

    /**
     * Get display name for a user.
     *
     * @return string Full name
     */
    public function getDisplayName(mixed $user): string
    {
        // Try to get full_name property
        if (isset($user->full_name)) {
            return $user->full_name;
        }

        // Try to concatenate first_name and last_name
        if (isset($user->first_name) && isset($user->last_name)) {
            return trim("{$user->first_name} {$user->last_name}");
        }

        // Try to get name property
        if (isset($user->name)) {
            return $user->name;
        }

        // Fallback
        return 'Unknown User';
    }

    /**
     * Check if entity is a team member vs individual user.
     */
    public function isTeamMember(mixed $entity): bool
    {
        // Check if entity is an Employee (team member) model
        return $entity instanceof \App\Models\Employee;
    }
}

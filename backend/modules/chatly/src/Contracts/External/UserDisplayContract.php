<?php

namespace Modules\Chatly\Contracts\External;

/**
 * Outgoing port for user display information (avatars, names, etc.).
 *
 * The main application provides helpers for user/company avatars.
 */
interface UserDisplayContract
{
    /**
     * Get avatar URL for a user or team member.
     *
     * @param  mixed  $user  User or Employee model
     * @return string Avatar URL
     */
    public function getAvatarUrl(mixed $user): string;

    /**
     * Get avatar URL for a company/organization.
     *
     * @param  mixed  $company  Company model
     * @return string Avatar URL
     */
    public function getCompanyAvatarUrl(mixed $company): string;

    /**
     * Get display name for a user.
     *
     * @return string Full name
     */
    public function getDisplayName(mixed $user): string;

    /**
     * Check if entity is a team member vs individual user.
     */
    public function isTeamMember(mixed $entity): bool;
}

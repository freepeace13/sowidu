<?php

namespace Packages\Tenancy;

use Illuminate\Database\Eloquent\Model;

class TestGuardStore implements TeamStore
{
    /**
     * In-memory storage for team IDs keyed by user ID.
     *
     * @var array<int, int|null>
     */
    protected static array $teamIds = [];

    /**
     * Set the team ID for the given user.
     *
     * @param  int|null  $teamId
     */
    public function setTeamId(Model $user, $teamId): void
    {
        static::$teamIds[$user->getKey()] = $teamId;
    }

    /**
     * Get the team ID for the given user.
     */
    public function getTeamId(Model $user): ?int
    {
        return static::$teamIds[$user->getKey()] ?? null;
    }

    /**
     * Clear all stored team IDs.
     * Useful for resetting state between tests.
     */
    public static function clear(): void
    {
        static::$teamIds = [];
    }

    /**
     * Clear team ID for a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Model|int  $user
     */
    public static function clearFor($user): void
    {
        $userId = $user instanceof Model ? $user->getKey() : $user;
        unset(static::$teamIds[$userId]);
    }
}

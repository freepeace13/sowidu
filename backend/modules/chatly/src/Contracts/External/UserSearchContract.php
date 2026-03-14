<?php

namespace Modules\Chatly\Contracts\External;

/**
 * Outgoing port for user/participant search functionality.
 *
 * The main application must provide an adapter that implements this interface.
 */
interface UserSearchContract
{
    /**
     * Search for users and team members by keyword.
     *
     * @param  string  $keyword  Search term
     * @param  array  $filters  Additional filters (e.g., 'except' => [...])
     * @param  int  $limit  Maximum results per category
     * @return array ['people' => [...], 'groups' => [...]]
     */
    public function search(string $keyword, array $filters = [], int $limit = 8): array;

    /**
     * Find a messageable entity by URN.
     *
     * @param  string  $urn  The URN identifier
     * @return mixed The messageable entity (User or TeamMembership)
     */
    public function findByUrn(string $urn): mixed;

    /**
     * Get the current authenticated user.
     *
     * @return mixed The authenticated user
     */
    public function currentUser(): mixed;

    /**
     * Get the current team context if applicable.
     *
     * @return mixed|null The current team or null
     */
    public function currentTeam(): mixed;
}

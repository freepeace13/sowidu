<?php

namespace App\Services\Chat;

use App\Models\Employee;
use App\Models\User;
use Modules\Chatly\Contracts\External\UserSearchContract;
use Packages\Urn\UrnManager;

/**
 * Adapter for user/participant search functionality.
 *
 * Implements the outgoing port defined by Chatly module.
 */
class UserSearchAdapter implements UserSearchContract
{
    /**
     * Search for users and team members by keyword.
     *
     * @param  string  $keyword  Search term
     * @param  array  $filters  Additional filters (e.g., 'except' => [...])
     * @param  int  $limit  Maximum results per category
     * @return array ['people' => [...], 'groups' => [...]]
     */
    public function search(string $keyword, array $filters = [], int $limit = 8): array
    {
        $currentUser = $this->currentUser();

        // Extract IDs to exclude from search
        $exceptIds = [];
        foreach ($filters['except'] ?? [] as $except) {
            if (($except['type'] ?? null) == User::class) {
                $exceptIds[] = $except['id'];
            }
        }

        // Search for people (individual users)
        $peopleQuery = User::search($keyword);

        // Exclude current user unless impersonating
        if (!$this->isImpersonating()) {
            $peopleQuery->where('id', '!=', $currentUser->getKey());
        } else {
            $peopleQuery->where('id', '!=', $currentUser->user_id);
        }

        // Apply exception filters
        if (!empty($exceptIds)) {
            $peopleQuery->whereNotIn('id', $exceptIds);
        }

        $people = $peopleQuery->limit($limit)->get()->map(function ($person) {
            return [
                'id' => $person->id,
                'name' => $person->full_name,
                'photo' => get_user_avatar_url($person),
                'type' => get_class($person),
                'is_user' => true,
            ];
        });

        // Search for groups (team memberships)
        $groupsQuery = Employee::search($keyword);

        if ($this->isImpersonating()) {
            $groupsQuery->where('user_id', '!=', $currentUser->user_id);
        } else {
            $groupsQuery->where('user_id', '!=', $currentUser->id);
        }

        $groups = $groupsQuery->get()
            ->groupBy(function ($item) {
                return $item->employer->name;
            })
            ->map(function ($members) {
                return $members->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->user->full_name,
                        'photo' => get_company_avatar_url($member->employer),
                        'type' => get_class($member),
                        'role' => $member->role,
                        'is_user' => false,
                        'organization' => [
                            'id' => $member->employer->id,
                            'name' => $member->employer->name,
                        ],
                    ];
                });
            });

        return [
            'people' => $people,
            'groups' => $groups,
        ];
    }

    /**
     * Find a messageable entity by URN.
     *
     * @param  string  $urn  The URN identifier
     * @return mixed The messageable entity (User or TeamMembership)
     */
    public function findByUrn(string $urn): mixed
    {
        return UrnManager::resolve($urn);
    }

    /**
     * Get the current authenticated user.
     *
     * @return mixed The authenticated user
     */
    public function currentUser(): mixed
    {
        return auth()->user();
    }

    /**
     * Get the current team context if applicable.
     *
     * @return mixed|null The current team or null
     */
    public function currentTeam(): mixed
    {
        return session('current_team');
    }

    /**
     * Check if the current request is impersonating another user.
     */
    protected function isImpersonating(): bool
    {
        return session()->has('impersonate_user_id');
    }
}

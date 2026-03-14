<?php

namespace App\Policies;

use App\Enums\Permissions;
use App\Models\Category;
use App\Models\User;
use App\Policies\Traits\HandlesTeamAuthorization;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    use HandlesAuthorization, HandlesTeamAuthorization, InteractsWithImpersonator;

    protected $teamId;

    public function __construct()
    {
        $this->teamId = $this->getCurrentTeamId();
    }

    public function before(User $user, $ability)
    {
        if (
            $this->isImpersonating() &&
            $this->isCompanyOwner($this->getCurrentEmployee(), $this->getCurrentTeam())
        ) {
            return true; // Current employee is the company owner
        }
    }

    public function update(User $user, Category $category)
    {
        if ($this->isImpersonating()) {
            return $this->canRepresentTeam(
                $user,
                $this->teamId,
                Permissions::CAN_MANAGE_ORGANIZATION_SETTINGS,
            );
        }

        return true;
    }

    public function create(User $user)
    {
        if ($this->isImpersonating()) {
            return $this->canRepresentTeam(
                $user,
                $this->teamId,
                Permissions::CAN_MANAGE_ORGANIZATION_SETTINGS,
            );
        }
    }

    public function destroy(User $user, Category $category)
    {
        if (!$this->ownedByAccount($category)) {
            return Response::deny('This category falls outside of your account.');
        }

        if ($this->isImpersonating()) {
            return $this->canRepresentTeam(
                $user,
                $this->teamId,
                Permissions::CAN_MANAGE_ORGANIZATION_SETTINGS,
            );
        }
    }

    protected function ownedByAccount(Category $category): bool
    {
        return $category->ownerable->is($this->account());
    }
}

<?php

namespace App\Policies;

use App\Enums\Permissions;
use App\Models\CatalogItem;
use App\Models\User;
use App\Policies\Traits\HandlesTeamAuthorization;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Auth\Access\HandlesAuthorization;

class CatalogItemPolicy
{
    use HandlesAuthorization;
    use HandlesTeamAuthorization;
    use InteractsWithImpersonator;

    public function before(User $user, $ability)
    {
        if (
            $this->isImpersonating() &&
            $this->isCompanyOwner($this->getCurrentEmployee(), $this->getCurrentCompany())
        ) {
            return true; // Current employee is the company owner
        }
    }

    public function create(User $user)
    {
        return $this->canRepresentTeam(
            $user,
            $this->getCurrentTeamId(),
            Permissions::CAN_CREATE_CATALOG_ITEMS,
        );
    }

    public function update(User $user, CatalogItem $catalogItem)
    {
        return $this->canRepresentTeam(
            $user,
            $this->getCurrentTeamId(),
            Permissions::CAN_CREATE_CATALOG_ITEMS,
        ) && $catalogItem->isOwnedByCompany($this->getCurrentCompany());
    }

    public function delete(User $user, CatalogItem $catalogItem)
    {
        return $this->canRepresentTeam(
            $user,
            $this->getCurrentTeamId(),
            Permissions::CAN_DELETE_CATALOG_ITEMS,
        ) && $catalogItem->isOwnedByCompany($this->getCurrentCompany());
    }
}

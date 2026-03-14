<?php

namespace Modules\Catalog\Policies;

use App\Policies\Traits\HandlesTeamAuthorization;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Catalog\Models\CatalogItem;
use Modules\Shared\Enums\Permissions;

class CatalogItemPolicy
{
    use HandlesAuthorization;
    use HandlesTeamAuthorization;
    use InteractsWithImpersonator;

    public function before($user, $ability)
    {
        if (
            $this->isImpersonating() &&
            $this->isCompanyOwner($this->getCurrentEmployee(), $this->getCurrentCompany())
        ) {
            return true; // Current employee is the company owner
        }
    }

    public function create($user)
    {
        return $this->canRepresentTeam(
            $user,
            $this->getCurrentTeamId(),
            Permissions::CAN_CREATE_CATALOG_ITEMS,
        );
    }

    public function update($user, CatalogItem $catalogItem)
    {
        return $this->canRepresentTeam(
            $user,
            $this->getCurrentTeamId(),
            Permissions::CAN_CREATE_CATALOG_ITEMS,
        ) && $catalogItem->isOwnedByCompany($this->getCurrentCompany());
    }

    public function delete($user, CatalogItem $catalogItem)
    {
        return $this->canRepresentTeam(
            $user,
            $this->getCurrentTeamId(),
            Permissions::CAN_DELETE_CATALOG_ITEMS,
        ) && $catalogItem->isOwnedByCompany($this->getCurrentCompany());
    }
}

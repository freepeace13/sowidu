<?php

namespace App\Policies;

use App\Enums\Permissions;
use App\Models\DeliveryTicket;
use App\Models\User;
use App\Policies\Traits\HandlesTeamAuthorization;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeliveryTicketPolicy
{
    use HandlesAuthorization;
    use HandlesTeamAuthorization;
    use InteractsWithImpersonator;

    public function before(User $user, $ability)
    {
        if (!$this->isImpersonating()) {
            return false;
        }
    }

    public function create(User $user)
    {
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_DELIVERY_TICKETS);
    }

    public function update(User $user, DeliveryTicket $deliveryTicket)
    {
        // Check if user is the owner or has permissions
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_DELIVERY_TICKETS)
            && $deliveryTicket->isOwnedByCompany($this->getCurrentCompany());
    }

    public function delete(User $user, DeliveryTicket $deliveryTicket)
    {
        // Check if user is the owner or has permissions
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_DELIVERY_TICKETS)
            && $deliveryTicket->isOwnedByCompany($this->getCurrentCompany());
    }

    public function manageMaterials(User $user, DeliveryTicket $deliveryTicket)
    {
        return $this->isAuthorizedTo(
            $user,
            Permissions::CAN_MANAGE_MATERIALS_TO_DELIVERY_TICKETS,
        ) && $deliveryTicket->isOwnedByCompany(
            $this->getCurrentCompany(),
        );
    }
}

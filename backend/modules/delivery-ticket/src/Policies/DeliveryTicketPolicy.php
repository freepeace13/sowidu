<?php

namespace Modules\DeliveryTicket\Policies;

use App\Enums\Permissions;
use App\Policies\Traits\HandlesTeamAuthorization;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\DeliveryTicket\Models\DeliveryTicket;

class DeliveryTicketPolicy
{
    use HandlesAuthorization;
    use HandlesTeamAuthorization;
    use InteractsWithImpersonator;

    public function before($user, $ability)
    {
        if (!$this->isImpersonating()) {
            return false;
        }
    }

    public function create($user)
    {
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_DELIVERY_TICKETS);
    }

    public function update($user, DeliveryTicket $deliveryTicket)
    {
        // Check if user is the owner or has permissions
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_DELIVERY_TICKETS)
            && $deliveryTicket->isOwnedByCompany($this->getCurrentCompany());
    }

    public function delete($user, DeliveryTicket $deliveryTicket)
    {
        // Check if user is the owner or has permissions
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_DELIVERY_TICKETS)
            && $deliveryTicket->isOwnedByCompany($this->getCurrentCompany());
    }

    public function manageMaterials($user, DeliveryTicket $deliveryTicket)
    {
        return $this->isAuthorizedTo(
            $user,
            Permissions::CAN_MANAGE_MATERIALS_TO_DELIVERY_TICKETS,
        ) && $deliveryTicket->isOwnedByCompany(
            $this->getCurrentCompany(),
        );
    }
}

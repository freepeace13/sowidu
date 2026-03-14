<?php

namespace Modules\Offer\Policies;

use App\Models\User;
use App\Policies\Traits\HandlesTeamAuthorization;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Offer\Enums\Permissions;
use Modules\Offer\Models\Offer;
use Modules\Offer\OfferService;

class OfferPolicy
{
    use HandlesAuthorization;
    use HandlesTeamAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        return null;
    }

    public function create(User $user): bool
    {
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_OFFERS->value);
    }

    public function update(User $user, Offer $offer): bool
    {
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_OFFERS->value)
            && $offer->isOwnedByCompany($this->getCurrentCompany());
    }

    public function edit(User $user, Offer $offer): bool
    {
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_OFFERS->value)
            && $offer->isOwnedByCompany($this->getCurrentCompany())
            && $offer->isDraft();
    }

    public function delete(User $user, Offer $offer): bool
    {
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_OFFERS->value)
            && $offer->isOwnedByCompany($this->getCurrentCompany()) && $offer->isDraft();
    }

    public function manageItems(User $user, Offer $offer): bool
    {
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_OFFERS_ITEMS->value)
            && $offer->isOwnedByCompany($this->getCurrentCompany());
    }

    public function modifyStatus(User $user, Offer $offer): bool
    {
        return $this->isAuthorizedTo($user, Permissions::CAN_MODIFY_OFFERS_STATUS->value)
            && $offer->isOwnedByCompany($this->getCurrentCompany()) || $this->isRecipient($user, $offer);
    }

    public function send(User $user, Offer $offer): bool
    {
        return $this->modifyStatus($user, $offer)
            && $offer->isDraft();
    }

    public function reject(User $user, Offer $offer): bool
    {
        return $this->modifyStatus($user, $offer)
            && $offer->isSent();
    }

    public function accept(User $user, Offer $offer): bool
    {
        return ($this->modifyStatus($user, $offer) || $this->isRecipient($user, $offer))
            && $offer->isSent();
    }

    protected function isRecipient(User $user, Offer $offer): bool
    {
        return OfferService::make($offer)->isRecipient($user);
    }

    public function cancel(User $user, Offer $offer): bool
    {
        return $this->modifyStatus($user, $offer);
    }
}

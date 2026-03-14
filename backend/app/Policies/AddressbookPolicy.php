<?php

namespace App\Policies;

use App\Enums\Permissions;
use App\Models\AddressBook;
use App\Models\User;
use App\Policies\Traits\HandlesTeamAuthorization;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddressbookPolicy
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

    public function create(User $user)
    {
        if ($this->isImpersonating()) {
            return $this->canRepresentTeam(
                $user,
                $this->teamId,
                Permissions::CAN_MANAGE_ADDRESS_BOOK,
            );
        }

        return true;
    }

    public function update(User $user, Addressbook $addressbook)
    {
        if ($this->isImpersonating()) {
            return $this->canRepresentTeam(
                $user,
                $this->teamId,
                Permissions::CAN_MANAGE_ADDRESS_BOOK,
            ) && $addressbook->isOwnedByTeam($this->teamId);
        }

        // Ensure `user` is the addressbook `owner`
        return $addressbook->isOwnedByUser($user);
    }

    public function remove(User $user, Addressbook $addressbook)
    {
        if ($this->isImpersonating()) {
            return $this->canRepresentTeam(
                $user,
                $this->teamId,
                Permissions::CAN_MANAGE_ADDRESS_BOOK,
            ) && $addressbook->isOwnedByTeam($this->teamId);
        }

        // User (not Impersonating)
        return $addressbook->isOwnedByUser($user);
    }

    public function addMember(User $user, Addressbook $organizationAddressbook)
    {
        if ($this->isImpersonating()) {
            return $this->canRepresentTeam(
                $user,
                $this->teamId,
                Permissions::CAN_MANAGE_ADDRESS_BOOK,
            ) && $organizationAddressbook->isOwnedByTeam($this->teamId);
        }

        // User (not Impersonating)
        if (!$this->teamId) {
            // Ensure `user` is the addressbook `owner`
            return $organizationAddressbook->isOwnedByUser($user);
        }
    }

    public function updateMember(
        User $user,
        Addressbook $organizationAddressbook,
        Addressbook $memberAddressbook,
    ) {
        if ($this->isImpersonating()) {
            return $this->canRepresentTeam(
                $user,
                $this->teamId,
                Permissions::CAN_MANAGE_ADDRESS_BOOK,
            ) && $organizationAddressbook->isOwnedByTeam($this->teamId);
        }

        // Ensure `user` is the addressbook `owner`
        return $organizationAddressbook->isOwnedByUser($user) && $memberAddressbook->isOwnedByUser($user);
    }

    public function removeMember(
        User $user,
        Addressbook $organizationAddressbook,
        Addressbook $memberAddressbook,
    ) {
        if ($this->isImpersonating()) {
            return $this->canRepresentTeam(
                $user,
                $this->teamId,
                Permissions::CAN_MANAGE_ADDRESS_BOOK,
            ) && $organizationAddressbook->isOwnedByTeam($this->teamId);
        }

        // User (not Impersonating)
        return $organizationAddressbook->isOwnedByUser($user) && $memberAddressbook->isOwnedByUser($user);
    }
}

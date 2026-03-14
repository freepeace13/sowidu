<?php

namespace App\Actions\Addressbook\Organization\Member;

use App\Http\Controllers\Traits\AddressbookTrait;
use App\Models\Addressbook;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AddressbookRemoveOrganizationMember
{
    use AddressbookTrait;

    public function remove(User $user, Addressbook $organization, Addressbook $member, ?int $teamId = null)
    {
        Gate::forUser($user, 'removeMember', [$organization, $member]);

        throw_if($organization->doesntHaveMember($member), 'This member is not belongs to this organization.');

        $organization->organizationMembers()
            ->detach($member);
    }
}

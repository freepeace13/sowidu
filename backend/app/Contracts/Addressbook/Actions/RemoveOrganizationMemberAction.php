<?php

namespace App\Contracts\Addressbook\Actions;

use App\Models\Addressbook;
use App\Models\User;

interface RemoveOrganizationMemberAction
{
    public function remove(User $user, Addressbook $organization, string $urn);
}

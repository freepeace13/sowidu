<?php

namespace App\Contracts\Addressbook\Actions;

use App\Models\Addressbook;
use App\Models\User;

interface AddOrganizationMemberAction
{
    public function add(User $user, Addressbook $organization, string $urn);
}

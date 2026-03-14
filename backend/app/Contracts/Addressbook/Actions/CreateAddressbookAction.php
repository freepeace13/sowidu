<?php

namespace App\Contracts\Addressbook\Actions;

use App\Models\Addressbook;
use App\Models\User;

interface CreateAddressbookAction
{
    public function create(User $user, array $inputs, $teamId = null): Addressbook;
}

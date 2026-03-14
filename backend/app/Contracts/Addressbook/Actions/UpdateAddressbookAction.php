<?php

namespace App\Contracts\Addressbook\Actions;

use App\Models\Addressbook;
use App\Models\User;

interface UpdateAddressbookAction
{
    public function update(
        User $user,
        Addressbook $addressbook,
        array $inputs,
        $teamId = null,
    ): Addressbook;
}

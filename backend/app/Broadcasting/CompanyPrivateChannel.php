<?php

namespace App\Broadcasting;

use Account;
use App\Models\User;

class CompanyPrivateChannel
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param  object  $user
     * @param  int  $userId
     * @return array|bool
     */
    public function join($user, int $companyId)
    {
        return Account::current()->id === $companyId;
    }
}

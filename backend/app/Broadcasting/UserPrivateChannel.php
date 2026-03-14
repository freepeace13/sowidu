<?php

namespace App\Broadcasting;

use Account;
use Illuminate\Contracts\Auth\Access\Authorizable;

class UserPrivateChannel
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param  mixed  $user
     * @param  string  $userId
     * @return array|bool
     */
    public function join(Authorizable $user, $userId)
    {
        return Account::group($user)->id === (int) $userId;
    }
}

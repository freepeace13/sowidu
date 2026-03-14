<?php

namespace App\Broadcasting;

use Account;
use App\Models\User;

class EmployeePrivateChannel
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param  object  $user
     * @return array|bool
     */
    public function join($user, int $employeeId)
    {
        return Account::employeeId() === $employeeId;
    }
}

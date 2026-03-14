<?php

namespace App\Listeners;

use App\Actions\CreateCompanyEmployee;
use App\Models\User;

class CreateUsersEmployeeAccount
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $company = $event->invitation->company;
        $email = $event->invitation->email;
        $role = $event->invitation->role;

        $user = User::whereEmail($email)->first();

        (new CreateCompanyEmployee)->execute($company, $user, $role);
    }
}

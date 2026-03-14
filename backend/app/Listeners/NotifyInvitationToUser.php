<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\NewCompanyInvitation;

class NotifyInvitationToUser
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($user = User::firstWhere('email', $event->invitation->email)) {
            $user->notify(new NewCompanyInvitation($event->invitation));
        }
    }
}

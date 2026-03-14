<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\NewTeamMember;

class NotifyNewTeamMember
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

        $user = User::firstWhere('email', $event->invitation->email);

        if ($user) {
            $newMember = $company->employees()
                ->where('user_id', $user->getKey())
                ->first();

            $company->employees->each(function ($employee) use ($newMember) {
                if (!$employee->is($newMember)) {
                    $employee->notify(new NewTeamMember($newMember));
                }
            });
        }
    }
}

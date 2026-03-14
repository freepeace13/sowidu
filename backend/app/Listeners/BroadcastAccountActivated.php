<?php

namespace App\Listeners;

use App\Events\Contact\RelationshipUpdate;
use App\Models\Company;
use App\Models\User;

class BroadcastAccountActivated
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        collect()
            ->merge(User::confirmed()->get())
            ->merge(Company::confirmed()->get())
            ->each(function ($subscriber) use ($event) {

                if (!$event->account->is($subscriber)) {
                    RelationshipUpdate::broadcast($subscriber, $event->account);

                    if ($event->account instanceof User) {
                        $companiesQuery = $event->account->companies()->confirmed();

                        $companiesQuery->each(function ($company) {
                            $company->forceFill(['confirmed' => true])->save();

                            RelationshipUpdate::broadcast($subscriber, $event->account);
                        });
                    }
                }

            });
    }
}

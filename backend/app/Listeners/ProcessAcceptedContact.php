<?php

namespace App\Listeners;

use App\Models\Company;
use App\Models\Employee;

class ProcessAcceptedContact
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $sender = $event->contactship->sender;
        $recipient = $event->contactship->recipient;

        $account = $sender->employer ?? $sender;

        if (is_a($recipient, Employee::class)) {
            $account->createContact($recipient->employer);

            $recipient->employer->createContact($account);

            if (is_a($sender, Employee::class)) {
                $recipient->employer->createContact($sender);
            }
        } elseif (is_a($recipient, Company::class)) {
            $recipient->createContact($account);

            if (is_a($sender, Employee::class)) {
                $recipient->createContact($sender);
            }
        }

        $account->createContact($recipient);
    }
}

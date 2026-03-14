<?php

namespace App\Listeners;

class CreateEmploymentContacts
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $employee = $event->employee;
        $employer = $employee->employer;
        $applicant = $employee->user;
        $invitationId = $employee->invitation_id;

        $employer->createContact($applicant, $invitationId);
        $employer->createContact($employee, $invitationId);

        $applicant->createContact($employer, $invitationId);
        $applicant->createContact($employee, $invitationId);
    }
}

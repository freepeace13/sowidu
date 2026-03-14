<?php

namespace App\Listeners;

use App\Actions\CreateCompanyEmployee;
use App\Events\CompanyFounderCreated;
use App\Models\Company;

class CreateCompanyFounder
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        (new CreateCompanyEmployee)
            ->execute($event->company, $event->company->user, Company::FOUNDER_ROLE_NAME);

        CompanyFounderCreated::dispatch($event->company);
    }
}

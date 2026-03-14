<?php

namespace App\Listeners\Organization;

use App\Events\NewCompanyRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;

class SaveCompanyDefaultSettings implements ShouldQueue
{
    public function handle(NewCompanyRegistered $event)
    {
        $company = $event->company;
        $company->settings()->saveDefaults();
    }
}

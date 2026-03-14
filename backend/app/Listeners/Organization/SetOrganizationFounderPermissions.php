<?php

namespace App\Listeners\Organization;

use App\Actions\Organization\AssignFounderPermissions;
use App\Events\CompanyFounderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SetOrganizationFounderPermissions implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(CompanyFounderCreated $event)
    {
        (new AssignFounderPermissions)->execute($event->company);
    }
}

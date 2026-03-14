<?php

namespace App\Listeners\Todo;

use App\Events\CompanyFounderCreated;
use App\Factories\Todo\PredefinedBoardFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreatePredefinedBoardsOnNewCompany implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  App\Events\CompanyFounderCreated  $event
     * @return void
     */
    public function handle(CompanyFounderCreated $event)
    {
        PredefinedBoardFactory::make($event->company->user, $event->company);
    }
}

<?php

namespace App\Listeners\Todo;

use App\Events\Employment\EmployeeLeaved;
use App\Models\Board;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RemoveEmployeeToCompanyBoard implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(EmployeeLeaved $event)
    {
        $leaver = $event->employee;

        Board::query()
            ->whereHas('team', fn ($query) => $query->whereKey($event->company->getKey()))
            ->get()
            ->each(function (Board $board) use ($leaver) {
                $subscription = $board->getSubscriber($leaver->user);
                $board->users()->detach($subscription->user);
            });
    }
}

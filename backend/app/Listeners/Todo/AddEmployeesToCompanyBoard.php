<?php

namespace App\Listeners\Todo;

use App\Events\Todo\CompanyBoardCreated;
use App\Models\Subscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddEmployeesToCompanyBoard implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(CompanyBoardCreated $event)
    {
        $board = $event->board
            ->loadMissing([
                'team.employees.user',
            ]);

        $owner = $event->board->owner();
        $board->team->employees
            ->each(function (\App\Models\Employee $employee) use ($board, $owner) {
                // Owner is already subscribed to this board
                if ($employee->user->is($owner)) {
                    return;
                }

                $board->addSubscriber(
                    $employee->user,
                    Subscriber::DEFAULT_ROLE,
                );
            });
    }
}

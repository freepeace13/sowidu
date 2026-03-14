<?php

namespace App\Listeners\Auth;

use App\Factories\Todo\PredefinedBoardFactory;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreatePredefinedTodoBoards implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(Verified $event)
    {
        PredefinedBoardFactory::make($event->user);
    }
}

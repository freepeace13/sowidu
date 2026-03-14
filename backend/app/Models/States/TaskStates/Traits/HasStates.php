<?php

namespace App\Models\States\TaskStates\Traits;

use App\Models\States\BaseStatesTrait;
use App\Models\States\TaskStates\ArchivedState;
use App\Models\States\TaskStates\FinishedState;
use App\Models\States\TaskStates\InProgressState;
use App\Models\States\TaskStates\OpenState;
use App\Models\States\TaskStates\ProgressState;

trait HasStates
{
    use BaseStatesTrait;

    /**
     * Register model state transitions
     */
    public function registerStates(): void
    {
        $this
            ->addState('state', ProgressState::class)
            ->allowTransition(OpenState::class, InProgressState::class)
            ->allowTransition(OpenState::class, ArchivedState::class)
            ->allowTransition(ArchivedState::class, OpenState::class)
            ->allowTransition(InProgressState::class, FinishedState::class)
            ->allowTransition(FinishedState::class, ArchivedState::class)
            ->default(OpenState::class);
    }
}

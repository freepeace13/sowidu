<?php

namespace App\Models\States\OrderStates;

use Spatie\ModelStates\State;

abstract class ProgressState extends State
{
    /**
     * @var array
     */
    public static $states = [
        PreparationState::class,
        CompletedState::class,
        PendingState::class,
        FinalState::class,
        DoneState::class,
        CancelledState::class,
    ];

    /**
     * @var array
     */
    public static $steps = [
        PreparationState::class,
        CompletedState::class,
        PendingState::class,
        FinalState::class,
        DoneState::class,
    ];

    abstract public function color(): string;
}

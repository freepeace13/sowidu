<?php

namespace App\Models\States\TaskStates;

use Spatie\ModelStates\State;

class ProgressState extends State
{
    /**
     * @var array
     */
    public static $states = [
        OpenState::class,
        InProgressState::class,
        FinishedState::class,
        ArchivedState::class,
    ];

    /**
     * @var array
     */
    public static $steps = [
        OpenState::class,
        InProgressState::class,
        FinishedState::class,
    ];
}

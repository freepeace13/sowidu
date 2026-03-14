<?php

namespace App\Models\States\InvitationStates;

use Spatie\ModelStates\State;

abstract class ApprovalState extends State
{
    /**
     * @var array
     */
    public static $states = [
        AcceptedState::class,
        PendingState::class,
    ];

    /**
     * @var array
     */
    public static $steps = [
        AcceptedState::class,
        PendingState::class,
    ];
}

<?php

namespace App\Models\States\InvitationStates\Traits;

use App\Models\States\BaseStatesTrait;
use App\Models\States\InvitationStates;

trait HasStates
{
    use BaseStatesTrait;

    /**
     * Register model state transitions
     */
    public function registerStates(): void
    {
        $this
            ->addState('state', InvitationStates\ApprovalState::class)
            ->allowTransition(
                InvitationStates\PendingState::class,
                InvitationStates\AcceptedState::class,
                InvitationStates\Transitions\PendingToAccepted::class,
            )
            ->default(InvitationStates\PendingState::class);
    }
}

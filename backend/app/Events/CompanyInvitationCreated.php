<?php

namespace App\Events;

use App\Models\CompanyInvitation as Invitation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyInvitationCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $invitation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }
}

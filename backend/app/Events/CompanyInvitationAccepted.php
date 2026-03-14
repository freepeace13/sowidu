<?php

namespace App\Events;

use App\Models\CompanyInvitation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyInvitationAccepted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $invitation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CompanyInvitation $invitation)
    {
        $this->invitation = $invitation;
    }
}

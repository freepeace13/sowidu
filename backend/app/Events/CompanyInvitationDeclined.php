<?php

namespace App\Events;

use App\Models\CompanyInvitation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyInvitationDeclined
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

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

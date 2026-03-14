<?php

namespace App\Events\Invitation;

use App\Factories\Account;
use App\Http\Resources\InvitationResource;
use App\Models\Invitation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \App\Models\Invitation
     */
    public $invitation;

    public $subscriber;

    public $contactable;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Invitation
     * @return void
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
        $this->contactable = $this->invitation->recipient;
        $this->subscriber = Account::group($this->invitation->sender);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(model_morphs_stringify($this->invitation->recipient));
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return (new InvitationResource($this->invitation))->resolve();
    }
}

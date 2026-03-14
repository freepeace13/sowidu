<?php

namespace App\Notifications\Composers;

use App\Contracts\NotificationComposer;
use App\Models\Invitation;

class InvitationAcceptedComposer implements NotificationComposer
{
    /**
     * @var \App\Models\Invitation
     */
    protected $invitation;

    /**
     * Create new notification composer instance.
     *
     * @return void
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * @return string
     */
    public function avatar()
    {
        return $this->invitation->recipient->avatarUrl;
    }

    /**
     * @return string
     */
    public function title()
    {
        $recipientName = guess_name($this->invitation->recipient);
        $invitationType = $this->invitation->type;

        return "{$recipientName} has accepted your {$invitationType} invitation.";
    }

    /**
     * @return string
     */
    public function subtitle()
    {
        return $this->invitation->created_at->diffForHumans();
    }
}

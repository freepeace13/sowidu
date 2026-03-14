<?php

namespace App\Notifications\Composers;

use App\Contracts\NotificationComposer;
use App\Models\Invitation;

class InvitationSentComposer implements NotificationComposer
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
        return $this->invitation->sender->avatarUrl;
    }

    /**
     * @return string
     */
    public function title()
    {
        $senderName = guess_name($this->invitation->sender);
        $invitationType = $this->invitation->type;

        return "{$senderName} has sent you {$invitationType} invitation.";
    }

    /**
     * @return string
     */
    public function subtitle()
    {
        return $this->invitation->created_at->diffForHumans();
    }
}

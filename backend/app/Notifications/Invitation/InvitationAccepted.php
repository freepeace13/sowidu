<?php

namespace App\Notifications\Invitation;

use App\Models\Invitation;
use App\Notifications\Common\AbstractNotification;

class InvitationAccepted extends AbstractNotification
{
    /**
     * @var App\Models\Invitation
     */
    protected $invitation;

    /**
     * Create a new notification instance.
     *
     * @param  App\Models\Invitation  $invitation
     * @return void
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * The notification avatar.
     *
     * @return string
     */
    protected function avatar()
    {
        return $this->invitation->recipient->avatar_url;
    }

    /**
     * The notification message pattern.
     *
     * @return string
     */
    protected function message()
    {
        return ':subject accepted your :type invitation.';
    }

    /**
     * The message string attributes
     *
     * @return array
     */
    protected function attributes()
    {
        return [
            'subject' => $this->invitation->recipient->full_name,
            'type' => $this->invitation->type,
        ];
    }
}

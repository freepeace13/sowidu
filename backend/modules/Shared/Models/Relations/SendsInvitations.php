<?php

namespace Modules\Shared\Models\Relations;

use App\Events\Invitation\InvitationSent;
use App\Models\Invitation;
use App\Models\States\InvitationStates\PendingState;
use Illuminate\Database\Eloquent\Model;

trait SendsInvitations
{
    /**
     * Sends or update the existing invitation
     *
     * @return \App\Models\Invitation
     */
    public function sendInvitation(Invitation $invitation)
    {
        /**
         * If the sender has existing invitation we
         * will update the note and invitation type.
         */
        if ($this->hasSentInvitationTo($invitation->recipient, $invitation->type)) {
            $invitation = $this
                ->findSentInvitation($invitation->recipient, $invitation->type)
                ->fill($invitation->only('note', 'type'));
        }

        $invitation->fill(['sender' => $this])->save();

        InvitationSent::broadcast($invitation);

        return $invitation;
    }

    /**
     * Determine has already sent an invitation to the given recipient.
     *
     * @param  string|null  $type
     * @return bool
     */
    public function hasSentInvitationTo(Model $recipient, $type = null)
    {
        return !is_null($this->findSentInvitation($recipient, $type));
    }

    /**
     * Find the sent invitation to the given recipient.
     *d
     *
     * @param  string|null  $type
     * @return void
     */
    public function findSentInvitation(Model $recipient, $type = null)
    {
        return $this->invitationSenderQuery($recipient)
            ->whereState('state', PendingState::class)
            ->whereType($type)
            ->first();
    }

    /**
     * The invitation sender query builder.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function invitationSenderQuery(Model $recipient)
    {
        return Invitation::whereSender($this)->whereRecipient($recipient);
    }
}

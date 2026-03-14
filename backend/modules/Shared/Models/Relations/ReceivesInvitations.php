<?php

namespace Modules\Shared\Models\Relations;

use App\Exceptions\UnauthorizedException;
use App\Models\Invitation;
use App\Models\States\InvitationStates\PendingState;
use Illuminate\Database\Eloquent\Model;

trait ReceivesInvitations
{
    /**
     * Change invitation state to accepted and create contacts/employee needed.
     *
     * @return \App\Models\Invitation
     */
    public function acceptInvitation(Invitation $invitation)
    {
        // Invitation state transition to accepted state
        if (!$invitation->recipient->is($this)) {
            throw new UnauthorizedException('Unauthorize invitation acceptance.');
        }

        return $invitation->accept();
    }

    /**
     * Get all recipient's invitations
     *
     * @return \Illuminate\Support\Collection
     */
    public function getInvitations()
    {
        return Invitation::whereRecipient($this)
            ->whereState('state', PendingState::class)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all recipient's invitations
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllInvitations()
    {
        return Invitation::whereRecipient($this)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find the invitation sent from the given sender.
     *
     * @param  string|null  $type
     * @return void
     */
    public function findInvitationFrom(Model $sender, $type = null)
    {
        return $this->invitationRecipientQuery($sender)
            ->whereState('state', PendingState::class)
            ->whereType($type)
            ->first();
    }

    /**
     * Determine has invitation from givenWhereS
     *
     * @param  string|null  $type
     * @return bool
     */
    public function hasInvitationFrom(Model $sender, $type = null)
    {
        return !is_null($this->findInvitationFrom($sender, $type));
    }

    /**
     * The invitation recipient query builder.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function invitationRecipientQuery(Model $sender)
    {
        return Invitation::whereRecipient($this)->whereSender($sender);
    }
}

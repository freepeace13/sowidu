<?php

namespace App\Models\States\InvitationStates\Transitions;

use Account;
use App\Models\Employee;
use App\Models\Invitation;
use App\Models\States\InvitationStates\AcceptedState;
use App\Models\States\InvitationStates\PendingState;
use Spatie\ModelStates\Transition;

class PendingToAccepted extends Transition
{
    /**
     * @var App\Models\Invitation
     */
    private $invitation;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    private $recipient;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    private $sender;

    /**
     * Create new model state transition instance.
     *
     * @param  App\Models\invitation  $invitation
     * @return void
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
        $this->sender = $invitation->sender;
        $this->recipient = $invitation->recipient;
    }

    /**
     * Determine if the transition is allowed.

     *this->senderodelStates\Exceptions\TransitionNotAllowed
     */
    public function canTransition(): bool
    {
        return $this->invitation->state->is(PendingState::class);
    }

    /**
     * Handle other stuff upon changing state.
     *
     * @return App\Models\Invitation
     */
    public function handle()
    {
        $invitationId = $this->invitation->id;

        $this->invitation->state = AcceptedState::class;
        $this->invitation->save();

        if ($this->sender instanceof Employee) {
            $this->recipient->createContact($this->sender->employer, $invitationId);

            if ($this->invitation->type === Invitation::EMPLOYMENT) {
                $employee = $this->sender->employer->employees()->create([
                    'user_id' => $this->invitation->recipient_id,
                    'specialization_id' => 1, // TODO: Should be nullable
                    'confirmed' => true,
                    'invitation_id' => $invitationId,
                ]);

                $this->recipient->createContact($employee, $invitationId);
                Account::group($this->sender)->createContact($employee, $invitationId);
            }
        }

        Account::group($this->sender)->createContact($this->recipient, $invitationId);
        $this->recipient->createContact($this->sender, $invitationId);

        return $this->invitation;
    }
}

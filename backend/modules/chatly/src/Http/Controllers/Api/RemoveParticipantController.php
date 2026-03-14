<?php

namespace Modules\Chatly\Http\Controllers\Api;

use Modules\Chatly\Contracts\RemovesConversationParticipants;
use Musonza\Chat\Models\Conversation;
use Musonza\Chat\Models\Participation;

class RemoveParticipantController extends ApiController
{
    public function __construct(
        protected RemovesConversationParticipants $action,
    ) {}

    public function __invoke(Conversation $conversation, Participation $participation)
    {
        $participant = $this->currentParticipant();

        return $this->response($this->action->remove(
            $participant, $conversation, $participation,
        ));
    }
}

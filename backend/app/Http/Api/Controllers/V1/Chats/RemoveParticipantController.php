<?php

namespace App\Http\Api\Controllers\V1\Chats;

use App\Contracts\Actions\RemovesConversationParticipants;
use Musonza\Chat\Models\Conversation;
use Musonza\Chat\Models\Participation;

class RemoveParticipantController extends ChatsBaseController
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

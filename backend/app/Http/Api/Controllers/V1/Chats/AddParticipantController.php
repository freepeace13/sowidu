<?php

namespace App\Http\Api\Controllers\V1\Chats;

use App\Contracts\Actions\AddsConversationParticipants;
use App\Http\Api\Resources\V1\Chat\ParticipantResource;
use Illuminate\Http\Request;
use Musonza\Chat\Models\Conversation;

class AddParticipantController extends ChatsBaseController
{
    public function __construct(
        protected AddsConversationParticipants $action,
    ) {}

    public function __invoke(Request $request, Conversation $conversation)
    {
        $participant = $this->currentParticipant();

        return new ParticipantResource($this->action->add(
            $participant, $request->urn, $conversation,
        ));
    }
}

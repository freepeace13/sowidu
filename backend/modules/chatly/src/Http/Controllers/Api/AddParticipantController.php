<?php

namespace Modules\Chatly\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Chatly\Contracts\AddsConversationParticipants;
use Modules\Chatly\Http\Resource\ParticipantResource;
use Musonza\Chat\Models\Conversation;

class AddParticipantController extends ApiController
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

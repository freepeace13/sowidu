<?php

namespace App\Http\Api\Controllers\V1\Chats;

use App\Http\Api\Resources\V1\Chat\ParticipantResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Musonza\Chat\Models\Conversation;

class GetParticipantsController extends ChatsBaseController
{
    public function __invoke(Request $request, Conversation $conversation)
    {
        $participant = $this->currentParticipant();

        Gate::forUser($participant)->authorize('showParticipants', $conversation);

        $participants = $conversation->participants()->get();

        return ParticipantResource::collection($participants);
    }
}

<?php

namespace App\Http\Api\Actions\Chats;

use App\Contracts\Actions\RemovesConversationParticipants;
use Illuminate\Support\Facades\Gate;
use Musonza\Chat\Models\Conversation;
use Musonza\Chat\Models\Participation;
use Packages\RestApi\RestApiAction;

class RemoveParticipant extends RestApiAction implements RemovesConversationParticipants
{
    public function remove($user, Conversation $conversation, Participation $participation, $errorBag = null)
    {
        Gate::forUser($user)->authorize('removeParticipants', [$conversation, $participation]);

        $conversation->removeParticipant($participation->messageable);

        return true;
    }
}

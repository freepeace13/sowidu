<?php

namespace App\Http\Api\Controllers\V1\Chats;

use App\Http\Api\Resources\V1\Chat\ConversationResource;
use Illuminate\Support\Facades\Gate;
use Musonza\Chat\Models\Conversation;

class ShowConversationController extends ChatsBaseController
{
    public function __invoke(Conversation $conversation)
    {
        $participant = $this->currentParticipant();

        Gate::forUser($participant)->authorize('show', $conversation);

        return (new ConversationResource($conversation))
            ->withParticipants();
    }
}

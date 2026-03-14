<?php

namespace App\Http\Api\Actions\Chats;

use App\Contracts\Actions\DeletesConversations;
use Illuminate\Support\Facades\Gate;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Conversation;
use Packages\RestApi\RestApiAction;

class DeleteConversation extends RestApiAction implements DeletesConversations
{
    public function delete($user, Conversation $conversation, $errorBag = null)
    {
        Gate::forUser($user)->authorize('delete', $conversation);

        Chat::conversation($conversation)
            ->setParticipant($user)
            ->clear();

        Chat::conversation($conversation)
            ->getParticipation($user)
            ->update(['settings' => ['mute_conversation' => true]]);

        return $conversation->fresh();
    }
}

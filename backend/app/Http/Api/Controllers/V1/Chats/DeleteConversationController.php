<?php

namespace App\Http\Api\Controllers\V1\Chats;

use App\Contracts\Actions\DeletesConversations;
use App\Http\Api\Resources\V1\Chat\ConversationResource;
use Illuminate\Http\Request;
use Musonza\Chat\Models\Conversation;

class DeleteConversationController extends ChatsBaseController
{
    public function __construct(
        protected DeletesConversations $action,
    ) {}

    public function __invoke(Request $request, Conversation $conversation)
    {
        $participant = $this->currentParticipant();

        $this->action->delete($participant, $conversation);

        return (new ConversationResource($conversation))
            ->withParticipants();
    }
}

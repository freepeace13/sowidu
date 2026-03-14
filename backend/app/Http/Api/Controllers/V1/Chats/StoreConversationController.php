<?php

namespace App\Http\Api\Controllers\V1\Chats;

use App\Contracts\Actions\CreatesConversations;
use App\Http\Api\Resources\V1\Chat\ConversationResource;
use Illuminate\Http\Request;
use Musonza\Chat\Models\Conversation;

class StoreConversationController extends ChatsBaseController
{
    public function __construct(
        protected CreatesConversations $creator,
    ) {}

    public function __invoke(Request $request, Conversation $conversation)
    {
        $participant = $this->currentParticipant();

        $conversation = $this->creator->create($participant, [
            'recipients' => $request->recipients, // array of URN's
            'message' => $request->message,
        ]);

        return new ConversationResource($conversation);
    }
}

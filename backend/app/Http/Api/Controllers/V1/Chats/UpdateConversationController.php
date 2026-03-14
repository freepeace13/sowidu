<?php

namespace App\Http\Api\Controllers\V1\Chats;

use App\Contracts\Actions\UpdatesConversations;
use App\Http\Api\Resources\V1\Chat\ConversationResource;
use Illuminate\Http\Request;
use Musonza\Chat\Models\Conversation;

class UpdateConversationController extends ChatsBaseController
{
    public function __construct(
        protected UpdatesConversations $updater,
    ) {}

    public function __invoke(Request $request, Conversation $conversation)
    {
        $participant = $this->currentParticipant();

        $data = $request->only(['name', 'avatar']);

        return new ConversationResource(
            $this->updater->update($participant, $conversation, $data),
        );
    }
}

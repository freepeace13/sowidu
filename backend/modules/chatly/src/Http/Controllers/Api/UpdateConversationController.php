<?php

namespace Modules\Chatly\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Chatly\Contracts\UpdatesConversations;
use Modules\Chatly\Http\Resource\ConversationResource;
use Musonza\Chat\Models\Conversation;

class UpdateConversationController extends ApiController
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

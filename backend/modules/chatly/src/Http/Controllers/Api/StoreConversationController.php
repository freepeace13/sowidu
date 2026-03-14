<?php

namespace Modules\Chatly\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Chatly\Contracts\CreatesConversations;
use Modules\Chatly\Http\Resource\ConversationResource;
use Musonza\Chat\Models\Conversation;

class StoreConversationController extends ApiController
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

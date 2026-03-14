<?php

namespace Modules\Chatly\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Chatly\Contracts\DeletesConversations;
use Modules\Chatly\Http\Resource\ConversationResource;
use Musonza\Chat\Models\Conversation;

class DeleteConversationController extends ApiController
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

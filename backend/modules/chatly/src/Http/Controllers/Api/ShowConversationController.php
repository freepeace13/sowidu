<?php

namespace Modules\Chatly\Http\Controllers\Api;

use Modules\Chatly\Contracts\External\AuthorizationContract;
use Modules\Chatly\Http\Resource\ConversationResource;
use Musonza\Chat\Models\Conversation;

class ShowConversationController extends ApiController
{
    public function __construct(
        protected AuthorizationContract $authorization,
    ) {}

    public function __invoke(Conversation $conversation)
    {
        $participant = $this->currentParticipant();

        $this->authorization->authorize($participant, 'show', $conversation);

        return (new ConversationResource($conversation))
            ->withParticipants();
    }
}

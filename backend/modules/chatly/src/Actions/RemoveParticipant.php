<?php

namespace Modules\Chatly\Actions;

use Modules\Chatly\Contracts\External\AuthorizationContract;
use Modules\Chatly\Contracts\RemovesConversationParticipants;
use Musonza\Chat\Models\Conversation;
use Musonza\Chat\Models\Participation;
use Packages\RestApi\RestApiAction;

class RemoveParticipant extends RestApiAction implements RemovesConversationParticipants
{
    public function __construct(
        protected AuthorizationContract $authorization,
    ) {}

    public function remove($user, Conversation $conversation, Participation $participation, $errorBag = null)
    {
        $this->authorization->authorize($user, 'removeParticipants', [$conversation, $participation]);

        $conversation->removeParticipant($participation->messageable);

        return true;
    }
}

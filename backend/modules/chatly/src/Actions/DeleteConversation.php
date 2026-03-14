<?php

namespace Modules\Chatly\Actions;

use Modules\Chatly\Contracts\DeletesConversations;
use Modules\Chatly\Contracts\External\AuthorizationContract;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Conversation;
use Packages\RestApi\RestApiAction;

class DeleteConversation extends RestApiAction implements DeletesConversations
{
    public function __construct(
        protected AuthorizationContract $authorization,
    ) {}

    public function delete($user, Conversation $conversation, $errorBag = null)
    {
        $this->authorization->authorize($user, 'delete', $conversation);

        Chat::conversation($conversation)
            ->setParticipant($user)
            ->clear();

        Chat::conversation($conversation)
            ->getParticipation($user)
            ->update(['settings' => ['mute_conversation' => true]]);

        return $conversation->fresh();
    }
}

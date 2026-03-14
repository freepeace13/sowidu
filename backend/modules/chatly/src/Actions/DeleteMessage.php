<?php

namespace Modules\Chatly\Actions;

use Modules\Chatly\Contracts\DeletesMessages;
use Modules\Chatly\Contracts\External\AuthorizationContract;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Message;
use Packages\RestApi\RestApiAction;

class DeleteMessage extends RestApiAction implements DeletesMessages
{
    public function __construct(
        protected AuthorizationContract $authorization,
    ) {}

    public function delete($user, Message $message, $errorBag = null)
    {
        $this->authorization->authorize($user, 'delete', $message);

        $participant = $message->conversation->participantFromSender($user);

        Chat::message($message)
            ->setParticipant($participant)
            ->delete();

        return true;
    }
}

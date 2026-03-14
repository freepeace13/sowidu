<?php

namespace App\Http\Api\Actions\Chats;

use App\Contracts\Actions\DeletesMessages;
use Illuminate\Support\Facades\Gate;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Message;
use Packages\RestApi\RestApiAction;

class DeleteMessage extends RestApiAction implements DeletesMessages
{
    public function delete($user, Message $message, $errorBag = null)
    {
        Gate::forUser($user)->authorize('delete', $message);

        $participant = $message->conversation->participantFromSender($user);

        Chat::message($message)
            ->setParticipant($participant)
            ->delete();

        return true;
    }
}

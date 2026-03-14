<?php

namespace App\Http\Api\Controllers\V1\Chats;

use App\Contracts\Actions\DeletesMessages;
use Illuminate\Http\Request;
use Musonza\Chat\Models\Message;

class DeleteMessageController extends ChatsBaseController
{
    public function __construct(
        protected DeletesMessages $action,
    ) {}

    public function __invoke(Request $request, Message $message)
    {
        $participant = $this->currentParticipant();

        return $this->response(
            $this->action->delete($participant, $message),
        );
    }
}

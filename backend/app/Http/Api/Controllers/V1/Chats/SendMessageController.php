<?php

namespace App\Http\Api\Controllers\V1\Chats;

use App\Contracts\Actions\CreatesMessages;
use App\Http\Api\Resources\V1\Chat\MessageResource;
use Illuminate\Http\Request;
use Musonza\Chat\Models\Conversation;

class SendMessageController extends ChatsBaseController
{
    public function __construct(
        protected CreatesMessages $creator,
    ) {}

    public function __invoke(Request $request, Conversation $conversation)
    {
        $participant = $this->currentParticipant();

        $message = $this->creator->create($participant, $conversation, [
            'message' => $request->message,
            'type' => $request->type,
            'data' => $request->data ?? [],
        ]);

        return new MessageResource($message);
    }
}

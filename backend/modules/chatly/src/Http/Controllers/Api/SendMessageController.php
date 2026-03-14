<?php

namespace Modules\Chatly\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Chatly\Contracts\CreatesMessages;
use Modules\Chatly\Http\Resource\MessageResource;
use Musonza\Chat\Models\Conversation;

class SendMessageController extends ApiController
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

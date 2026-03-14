<?php

namespace App\Http\Api\Controllers\V1\Chats;

use App\Http\Api\Resources\V1\Chat\MessageResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Conversation;

class GetMessagesController extends ChatsBaseController
{
    public function __invoke(Request $request, Conversation $conversation)
    {
        $participant = $this->currentParticipant();

        Gate::forUser($participant)->authorize('show', $conversation);

        $messages = Chat::conversation($conversation)
            ->setParticipant($participant)
            ->setPaginationParams([
                'sorting' => 'desc',
                'perPage' => $request->limit,
                'page' => $request->page ?? 1,
            ])
            ->getMessages();

        return MessageResource::collection($messages);
    }
}

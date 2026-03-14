<?php

namespace App\Http\Api\Controllers\V1\Chats;

use App\Http\Api\Resources\V1\Chat\ConversationResource;
use App\Models\ChatParticipation;
use Illuminate\Http\Request;

class GetConversationsController extends ChatsBaseController
{
    public function __invoke(Request $request)
    {
        $participant = $this->currentParticipant();

        $participations = ChatParticipation::query()
            ->ofUser($participant)
            ->withLastMessage($participant)
            ->orderByLastMessage()
            ->ignoreMuted()
            ->simplePaginate(
                perPage: $request->limit,
                page: $request->page,
            );

        $conversations = $participations->through(fn ($participation) => $participation->conversation);

        return ConversationResource::collection(
            $conversations,
            fn (ConversationResource $resource) => $resource
                ->withLastMessage()
                ->withParticipants(),
        );
    }
}

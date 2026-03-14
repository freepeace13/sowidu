<?php

namespace Modules\Chatly\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Chatly\Http\Resource\ConversationResource;
use Modules\Chatly\Models\Participation;

class GetConversationsController extends ApiController
{
    public function __invoke(Request $request)
    {
        $participant = $this->currentParticipant();

        $participations = Participation::query()
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

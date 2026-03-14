<?php

namespace App\Http\Api\Controllers\V1\Chats;

use App\Contracts\Actions\CreatesConversations;
use App\Http\Api\Resources\V1\Chat\ConversationResource;
use App\Models\ChatParticipation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Musonza\Chat\Models\Conversation;

class ConversationController extends ChatsBaseController
{
    public function index()
    {
        $participant = $this->currentParticipant();

        $participations = ChatParticipation::query()
            ->ofUser($participant)
            ->withLastMessage($participant)
            ->orderByLastMessage()
            ->ignoreMuted()
            ->simplePaginate();

        $conversations = $participations->through(fn ($participation) => $participation->conversation);

        return ConversationResource::collection(
            $conversations,
            fn (ConversationResource $resource) => $resource
                ->withLastMessage()
                ->withParticipants(),
        );
    }

    public function store(CreatesConversations $creator, Request $request)
    {
        $participant = $this->currentParticipant();

        $conversation = $creator->create($participant, [
            'recipients' => $request->recipients, // array of URN's
            'message' => $request->message,
        ]);

        return new ConversationResource($conversation);
    }

    public function show(Conversation $conversation)
    {
        $participant = $this->currentParticipant();

        Gate::forUser($participant)->authorize('show', $conversation);

        return (new ConversationResource($conversation))
            ->withParticipants();
    }
}

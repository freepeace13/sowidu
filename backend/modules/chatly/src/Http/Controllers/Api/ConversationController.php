<?php

namespace Modules\Chatly\Http\Controllers\Api;

use App\Models\ChatParticipation;
use Illuminate\Http\Request;
use Modules\Chatly\Contracts\CreatesConversations;
use Modules\Chatly\Contracts\External\AuthorizationContract;
use Modules\Chatly\Http\Resource\ConversationResource;
use Musonza\Chat\Models\Conversation;

class ConversationController extends ApiController
{
    public function __construct(
        protected AuthorizationContract $authorization,
    ) {}

    public function index()
    {
        $participant = $this->currentParticipant();

        $participations = ChatParticipation::query()
            ->ofUser($participant)
            ->withLastMessage($participant)
            ->orderByLastMessage()
            ->ignoreMuted()
            ->simplePaginate();

        /** @var \Illuminate\Contracts\Pagination\Paginator $conversations */
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

        $this->authorization->authorize($participant, 'show', $conversation);

        return (new ConversationResource($conversation))
            ->withParticipants();
    }
}

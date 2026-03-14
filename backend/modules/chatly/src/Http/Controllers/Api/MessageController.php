<?php

namespace Modules\Chatly\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Chatly\Contracts\CreatesMessages;
use Modules\Chatly\Contracts\External\AuthorizationContract;
use Modules\Chatly\Http\Resource\MessageResource;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Conversation;

class MessageController extends ApiController
{
    public function __construct(
        protected AuthorizationContract $authorization,
    ) {}

    public function index(Request $request, Conversation $conversation)
    {
        $participant = $this->currentParticipant();

        $this->authorization->authorize($participant, 'show', $conversation);

        $messages = Chat::conversation($conversation)
            ->setParticipant($participant)
            ->setPaginationParams([
                'page' => $request->page ?? 1,
                'sorting' => 'desc',
            ])
            ->getMessages();

        return MessageResource::collection($messages);
    }

    public function store(Request $request, Conversation $conversation, CreatesMessages $creator)
    {
        $participant = $this->currentParticipant();

        $this->authorization->authorize($participant, 'show', $conversation);

        $message = $creator->create($participant, $conversation, [
            'message' => $request->message,
            'type' => $request->type,
            'data' => $request->data ?? [],
        ]);

        return new MessageResource($message);
    }

    public function destroy(Request $request, $conversation, $message)
    {
        //
    }
}

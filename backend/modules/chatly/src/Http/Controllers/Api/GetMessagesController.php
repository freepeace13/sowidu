<?php

namespace Modules\Chatly\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Chatly\Contracts\External\AuthorizationContract;
use Modules\Chatly\Http\Resource\MessageResource;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Conversation;

class GetMessagesController extends ApiController
{
    public function __construct(
        protected AuthorizationContract $authorization,
    ) {}

    public function __invoke(Request $request, Conversation $conversation)
    {
        $participant = $this->currentParticipant();

        $this->authorization->authorize($participant, 'show', $conversation);

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

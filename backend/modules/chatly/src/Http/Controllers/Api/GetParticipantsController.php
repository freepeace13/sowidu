<?php

namespace Modules\Chatly\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Chatly\Contracts\External\AuthorizationContract;
use Modules\Chatly\Http\Resource\ParticipantResource;
use Musonza\Chat\Models\Conversation;

class GetParticipantsController extends ApiController
{
    public function __construct(
        protected AuthorizationContract $authorization,
    ) {}

    public function __invoke(Request $request, Conversation $conversation)
    {
        $participant = $this->currentParticipant();

        $this->authorization->authorize($participant, 'showParticipants', $conversation);

        $participants = $conversation->participants()->get();

        return ParticipantResource::collection($participants);
    }
}

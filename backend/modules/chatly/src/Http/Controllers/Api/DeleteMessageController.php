<?php

namespace Modules\Chatly\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Chatly\Contracts\DeletesMessages;
use Musonza\Chat\Models\Message;

class DeleteMessageController extends ApiController
{
    public function __construct(
        protected DeletesMessages $action,
    ) {}

    public function __invoke(Request $request, Message $message)
    {
        $participant = $this->currentParticipant();

        return $this->response(
            $this->action->delete($participant, $message),
        );
    }
}

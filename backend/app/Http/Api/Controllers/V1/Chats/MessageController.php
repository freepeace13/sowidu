<?php

namespace App\Http\Api\Controllers\V1\Chats;

use App\Contracts\Actions\CreatesMessages;
use App\Http\Api\Resources\V1\Chat\MessageResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Conversation;

class MessageController extends ChatsBaseController
{
    public function index(Request $request, Conversation $conversation)
    {
        $participant = $this->currentParticipant();

        Gate::forUser($participant)->authorize('show', $conversation);

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

        Gate::forUser($participant)->authorize('show', $conversation);

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

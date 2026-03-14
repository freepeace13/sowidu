<?php

namespace App\Http\Api\Actions\Chats;

use App\Contracts\Actions\CreatesConversations as CreatesConversationsContract;
use App\Contracts\Actions\CreatesMessages;
use Illuminate\Support\Arr;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Packages\RestApi\RestApiAction;
use Packages\Urn\UrnManager;

class CreateConversation extends RestApiAction implements CreatesConversationsContract
{
    protected $rules = [
        'message' => ['nullable', 'string'],
        'recipients' => ['required', 'array', 'min:1'],
        'recipients.*' => ['required', 'string'],
    ];

    public function __construct(protected CreatesMessages $messageCreator) {}

    public function create($user, array $params, $errorBag = null)
    {
        $validated = $this->validate($params, $errorBag);

        $recipients = array_map(fn ($recipient) => UrnManager::resolve($recipient), $validated['recipients']);

        $conversation = $this->startConversation([$user, ...$recipients]);

        if ($messageBody = Arr::get($validated, 'message')) {
            $this->messageCreator->create($user, $conversation, [
                'type' => 'text',
                'message' => $messageBody,
                'data' => [],
            ]);
        }

        return $conversation;
    }

    protected function startConversation($participants)
    {
        if (count($participants) > 2) {
            return Chat::makeDirect()->createConversation($participants);
        }

        return Chat::createConversation($participants);
    }
}

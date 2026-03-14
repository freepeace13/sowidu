<?php

namespace Modules\Chatly\Actions;

use Illuminate\Support\Arr;
use Modules\Chatly\Contracts\CreatesConversations;
use Modules\Chatly\Contracts\CreatesMessages;
use Modules\Chatly\Contracts\External\UrnResolverContract;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Packages\RestApi\RestApiAction;

class CreateConversation extends RestApiAction implements CreatesConversations
{
    protected $rules = [
        'message' => ['nullable', 'string'],
        'recipients' => ['required', 'array', 'min:1'],
        'recipients.*' => ['required', 'string'],
    ];

    public function __construct(
        protected CreatesMessages $messageCreator,
        protected UrnResolverContract $urnResolver,
    ) {}

    public function create($user, array $params, $errorBag = null)
    {
        $validated = $this->validate($params, $errorBag);

        $recipients = array_map(
            fn ($recipient) => $this->urnResolver->resolve($recipient),
            $validated['recipients'],
        );

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

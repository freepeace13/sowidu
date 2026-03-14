<?php

namespace Modules\Chatly\Actions;

use Modules\Chatly\Contracts\CreatesMessages;
use Modules\Chatly\Contracts\External\AuthorizationContract;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Conversation;
use Packages\RestApi\RestApiAction;

class CreateMessage extends RestApiAction implements CreatesMessages
{
    protected $rules = [
        'message' => 'string',
        'type' => 'in:text,image,attachment',
        'data' => 'array',
    ];

    public function __construct(
        protected AuthorizationContract $authorization,
    ) {}

    public function create($user, Conversation $conversation, array $params, $errorBag = null)
    {
        $this->authorization->authorize($user, 'sendMessage', $conversation);

        $params['message'] = $params['message'] ?? '';
        $params['type'] = $params['type'] ?? 'text';
        $params['data'] = $params['data'] ?? [];

        $validated = $this->validate($params, $errorBag);

        return Chat::message($validated['message'])
            ->type($validated['type'])
            ->data($validated['data'])
            ->from($user)
            ->to($conversation)
            ->send();
    }
}

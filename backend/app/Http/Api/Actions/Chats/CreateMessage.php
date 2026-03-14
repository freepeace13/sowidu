<?php

namespace App\Http\Api\Actions\Chats;

use App\Contracts\Actions\CreatesMessages as CreatesMessagesContract;
use Illuminate\Support\Facades\Gate;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Conversation;
use Packages\RestApi\RestApiAction;

class CreateMessage extends RestApiAction implements CreatesMessagesContract
{
    protected $rules = [
        'message' => 'string',
        'type' => 'in:text,image,attachment',
        'data' => 'array',
    ];

    public function create($user, Conversation $conversation, array $params, $errorBag = null)
    {
        Gate::forUser($user)->authorize('sendMessage', $conversation);

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

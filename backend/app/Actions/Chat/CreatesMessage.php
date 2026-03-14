<?php

namespace App\Actions\Chat;

use Illuminate\Support\Facades\Validator;
use Musonza\Chat\Facades\ChatFacade as Chat;
use Musonza\Chat\Models\Conversation;

class CreatesMessage
{
    public function create($user, Conversation $conversation, array $params)
    {
        $params['message'] = $params['message'] ?? '';
        $params['type'] = $params['type'] ?? 'text';
        $params['data'] = $params['data'] ?? [];

        Validator::make($params, [
            'message' => 'string',
            'type' => 'in:text,image,attachment',
            'data' => 'array',
        ])->validateWithBag('createMessage');

        return Chat::message($params['message'])
            ->type($params['type'])
            ->data($params['data'])
            ->from($user)
            ->to($conversation)
            ->send();
    }
}

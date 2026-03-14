<?php

namespace Modules\Chatly\Transformers;

use Modules\Shared\Transformer;
use Musonza\Chat\Services\ConversationService;

class ConversationTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'direct_message' => $this->direct_message,
            'private' => $this->private,
            'data' => $this->data,
        ];
    }

    public function withParticipants($filter = null)
    {
        return $this->state(function (array $attributes) use ($filter) {
            $this->participants->loadMissing(['messageable']);

            $participants = is_callable($filter)
                ? $this->participants->filter($filter)
                : $this->participants;

            return [
                'participants' => $participants
                    ->map(function ($participant) {
                        return $participant->messageable->getParticipantDetails();
                    })->values(),
            ];
        });
    }

    public function withLastMessage()
    {
        return $this->state(function (array $attributes) {
            if ($this->last_message) {
                $this->last_message->loadMissing(['participation.messageable']);
            }

            return [
                'last_message' => $this->last_message
                    ? (new MessageTransformer($this->last_message))->resolve()
                    : [],
            ];
        });
    }

    public function withUnreadCount($conversation, $user)
    {
        return $this->state(function () use ($conversation, $user) {
            return [
                'unread_count' => (new ConversationService($conversation))->setParticipant($user)->unreadCount(),
            ];
        });
    }
}

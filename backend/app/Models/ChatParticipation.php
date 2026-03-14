<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Musonza\Chat\Models\Participation;

class ChatParticipation extends Participation
{
    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 15;

    public function scopeIgnoreMuted(Builder $query): Builder
    {
        return $query->whereJsonContains('settings', 'mute_conversation')
            ->orWhereNull('settings');
    }

    public function scopeOfUser(Builder $query, $user): Builder
    {
        return $query->where('chat_participation.messageable_id', $user->id)
            ->where('chat_participation.messageable_type', $user->getMorphClass());
    }

    public function scopeWithLastMessage(Builder $query, $participant): Builder
    {
        return $query->join(
            $this->tablePrefix . 'conversations as c',
            $this->tablePrefix . 'participation.conversation_id',
            '=',
            'c.id',
        )
            ->with([
                'conversation',
                'conversation.last_message' => function ($query) use ($participant) {
                    $query->join($this->tablePrefix . 'message_notifications', $this->tablePrefix . 'message_notifications.message_id', '=', $this->tablePrefix . 'messages.id')
                        ->select($this->tablePrefix . 'message_notifications.*', $this->tablePrefix . 'messages.*')
                        ->where($this->tablePrefix . 'message_notifications.messageable_id', $participant->getKey())
                        ->where($this->tablePrefix . 'message_notifications.messageable_type', $participant->getMorphClass())
                        ->whereNull($this->tablePrefix . 'message_notifications.deleted_at');
                },
                'conversation.participants.messageable',
            ]);
    }

    public function scopeOrderByLastMessage(Builder $query): Builder
    {
        return $query->orderBy('c.updated_at', 'DESC');

        return $query->join(
            $this->tablePrefix . 'message_notifications',
            $this->tablePrefix . 'message_notifications.message_id',
            '=',
            'chat_messages.id',
        );
    }
}

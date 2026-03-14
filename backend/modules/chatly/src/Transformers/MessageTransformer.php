<?php

namespace Modules\Chatly\Transformers;

use App\Support\Facades\Impersonate;
use Modules\Chatly\Repositories\ChatRepository;
use Modules\Shared\Transformer;

class MessageTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'is_attachment' => ChatRepository::messageIsAttachment($this->type),
            'participation_id' => $this->participation_id,
            'conversation_id' => $this->conversation_id,
            'sender' => $this->sender,
            'body' => $this->body,
            'data' => $this->data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_seen' => boolval($this->is_seen),
            'is_mine' => isset($this->sender['id']) && $this->sender['id'] == Impersonate::user()->id,
        ];
    }
}

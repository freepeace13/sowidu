<?php

namespace App\Transformers;

class NotificationTransformer extends Transformer
{
    public function toArray($request)
    {
        return array_merge([
            'id' => $this->id,
            'type' => $this->type,
            'is_unread' => $this->unread(),
            'notified_at' => $this->created_at->diffForHumans(),
        ], $this->data);
    }
}

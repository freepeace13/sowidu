<?php

namespace App\Transformers;

use App\Models\TaskComment;
use App\Transformers\Todo\TaskCommentTransformer;

class ActivityTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'description' => $this->resource->description,
            'properties' => $this->resource->properties,
            'causer' => (new UserTransformer($this->resource->causer))->resolve(),
            'event' => $this->resource->event,
            'created_at' => $this->resource->created_at,
        ];
    }

    public function withComment()
    {
        return $this->state(function (array $attributes) {
            if (!$comment = $this->resource->properties->get('comment', null)) {
                return [];
            }

            return [
                'comment' => (new TaskCommentTransformer(TaskComment::find($comment['id'])))
                    ->withOwner(auth_user())
                    ->resolve(),
            ];
        });
    }
}

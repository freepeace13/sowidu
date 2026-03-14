<?php

namespace Modules\Todos\Transformers;

use Modules\Todos\Models\Subscriber;

class TaskAttachmentTransformer extends Transformer
{
    public function __construct($resource)
    {
        parent::__construct(...func_get_args());
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'task_id' => $this->task_id,
            'author_id' => $this->author_id,
            'media_file_id' => $this->media_file_id,
            'path' => $this->path,
            'properties' => $this->properties,
            'is_mine' => $this->author_id == auth_user()?->id,
        ];
    }

    public function withIsMineTag(Subscriber $authSubscription)
    {
        return $this->state(function ($attributes) use ($authSubscription) {
            return [
                'is_mine' => $attributes['author_id'] == $authSubscription->id,
            ];
        });
    }
}

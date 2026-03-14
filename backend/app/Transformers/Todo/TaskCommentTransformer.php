<?php

namespace App\Transformers\Todo;

use App\Transformers\Transformer;
use App\Transformers\UserTransformer;

class TaskCommentTransformer extends Transformer
{
    public function __construct($resource)
    {
        parent::__construct(...func_get_args());
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'author_id' => $this->author_id,
            'message' => $this->message,
            'created_at' => $this->created_at,
            'author' => (new UserTransformer($this->author->user))->resolve(),
        ];
    }

    public function withOwner($user)
    {
        return $this->state(function ($attributes) use ($user) {
            return [
                'is_owner' => $this->isOwner($user),
            ];
        });
    }

    public function withTask()
    {
        return $this->state(function ($attributes) {
            return [
                'task' => (new TaskTransformer($this->task))->resolve(),
            ];
        });
    }
}

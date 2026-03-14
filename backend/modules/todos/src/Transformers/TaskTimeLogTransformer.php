<?php

namespace Modules\Todos\Transformers;

class TaskTimeLogTransformer extends Transformer
{
    public function __construct($resource)
    {
        parent::__construct(...func_get_args());
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'board_id' => $this->board_id,
            'task_id' => $this->task_id,
            'author_id' => $this->author_id,
            'date' => $this->date?->format('Y-m-d'),
            'duration' => $this->duration,
            'duration_text' => $this->duration_text,
            'description' => $this->description,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }

    public function withUser()
    {
        return $this->state(function () {
            return [
                'user' => (new UserTransformer($this->author->user))->resolve(),
            ];
        });
    }

    public function withTask()
    {
        return $this->state(function () {
            return [
                'task' => (new TaskTransformer($this->task))->resolve(),
            ];
        });
    }

    public function withIsOwnedFlag($user)
    {
        return $this->state(function () use ($user) {
            return [
                'is_owned' => $this->isOwner($user),
            ];
        });
    }
}

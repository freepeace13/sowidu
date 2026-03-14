<?php

namespace App\Transformers\Todo;

use App\Transformers\Transformer;
use App\Transformers\UserTransformer;

class SubscriberTransformer extends Transformer
{
    public function __construct($resource)
    {
        parent::__construct(...func_get_args());
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'settings' => $this->settings ?? [],
        ];
    }

    public function withBoard()
    {
        return $this->state(function ($attributes) {
            return [
                'board' => (new BoardTransformer($this->board))->resolve(),
            ];
        });
    }

    public function withUser()
    {
        return $this->state(function ($attributes) {
            return [
                'user' => (new UserTransformer($this->user))->resolve(),
            ];
        });
    }
}

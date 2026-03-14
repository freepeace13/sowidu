<?php

namespace App\Models\Strategies\Concerns;

trait AssertsMember
{
    public function assertMember($user)
    {
        return $this->model->hasMember($user);
    }
}

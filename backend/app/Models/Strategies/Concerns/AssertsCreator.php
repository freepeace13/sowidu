<?php

namespace App\Models\Strategies\Concerns;

trait AssertsCreator
{
    public function assertCreator($user)
    {
        return $this->model->creator->is($user);
    }
}

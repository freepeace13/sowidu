<?php

namespace App\Models\Foundations;

use Account;
use Illuminate\Database\Eloquent\Model;

abstract class Strategy
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function assertGroup(Model $user, $value)
    {
        return Account::group($user)->is($value);
    }

    protected function assertUser(Model $user, $value)
    {
        return $user->is($value);
    }

    protected function assertGroupOrUser(Model $user, $value)
    {
        return $this->assertGroup($user, $value)
            || $this->assertUser($user, $value);
    }
}

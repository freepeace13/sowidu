<?php

namespace App\Models\Strategies;

use App\Models\Foundations\Strategy;

class OrderStrategy extends Strategy
{
    use Concerns\AssertsCreator;
    use Concerns\AssertsMember;

    public function assertBiller($user)
    {
        return ($biller = optional($this->model->customer)->biller)
            ? $this->assertGroupOrUser($user, $biller)
            : false;
    }

    public function assertContractor($user)
    {
        return ($contractor = $this->model->contractor)
            ? $this->assertGroupOrUser($user, $contractor)
            : false;
    }

    public function assertState($state)
    {
        return $this->model->state->is($state);
    }
}

<?php

namespace App\Models\Strategies;

use App\Models\Foundations\Strategy;

class TaskStrategy extends Strategy
{
    use Concerns\AssertsCreator;
    use Concerns\AssertsMember;
}

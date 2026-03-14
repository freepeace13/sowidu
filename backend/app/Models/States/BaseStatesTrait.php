<?php

namespace App\Models\States;

use Spatie\ModelStates\HasStates;

trait BaseStatesTrait
{
    use HasStates;

    public static function getStateClass(string $field)
    {
        return self::getStateConfig()[$field]->stateClass;
    }
}

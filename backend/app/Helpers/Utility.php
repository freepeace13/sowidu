<?php

namespace App\Helpers;

class Utility
{
    public function getClassName($class)
    {
        return implode('', array_slice(explode('\\', get_class($class)), -1));
    }

    public function arrayKeysExist($keys, $searchable)
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $searchable)) {
                return false;
            }
        }

        return true;
    }

    public function randomNumbers($length = 10)
    {
        $numbers = range(0, 1000);
        shuffle($numbers);

        return implode('', array_slice($numbers, 0, $length));
    }
}

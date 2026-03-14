<?php

namespace App\Enums;

class Gender extends Enum
{
    const Male = 'male';
    const Female = 'female';

    public static function choices()
    {
        $results = [];

        foreach (self::getConstants() as $text => $value) {
            $results[] = compact('text', 'value');
        }

        return $results;
    }
}

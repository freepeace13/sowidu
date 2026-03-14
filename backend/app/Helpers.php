<?php

namespace App;

use Illuminate\Support\Str;

class Helpers
{
    public static function camelCaseArrayKeys($array, $levels = null)
    {
        foreach (array_keys($array) as $key) {
            // Get a reference to the value of the key (avoid copy)
            // Then remove that array element
            $value = &$array[$key];
            unset($array[$key]);

            // Transform key
            $transformedKey = static::camel($key);

            // Recurse
            if (is_array($value) && (is_null($levels) || --$levels > 0)) {
                $value = static::camelCaseArrayKeys($value, $levels);
            }

            // Store the transformed key with the referenced value
            $array[$transformedKey] = $value;

            // We'll be dealing with some large values, so memory cleanup is important
            unset($value);
        }

        return $array;
    }

    public static function camel($value)
    {
        // Preserve all caps
        if (strtoupper($value) === $value) {
            return $value;
        }

        return Str::camel($value);
    }
}

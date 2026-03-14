<?php

namespace App\Support;

use Illuminate\Support\Str;

class ApiHelpers
{
    /**
     * Recursively camel-case an array's keys
     *
     * @param  int|null  $levels  How many levels of an array keys to transform - by default recurse infinitely (null)
     * @return array $array
     */
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

    /**
     * Recursively snake-case an array's keys
     *
     * @param  int|null  $levels  How many levels of an array keys to transform - by default recurse infinitely (null)
     * @return array $array
     */
    public static function snakeCaseArrayKeys(array $array, $levels = null)
    {
        foreach (array_keys($array) as $key) {
            // Get a reference to the value of the key (avoid copy)
            // Then remove that array element
            $value = &$array[$key];
            unset($array[$key]);

            // Transform key
            $transformedKey = static::snake($key);

            // Recurse
            if (is_array($value) && (is_null($levels) || --$levels > 0)) {
                $value = static::snakeCaseArrayKeys($value, $levels);
            }

            // Store the transformed key with the referenced value
            $array[$transformedKey] = $value;

            // We'll be dealing with some large values, so memory cleanup is important
            unset($value);
        }

        return $array;
    }

    /**
     * Str::camel wrapper - for specific extra functionality
     * Note this is generally only applicable when dealing with API input/output key case
     *
     * @param  string  $value
     * @return string
     */
    public static function camel($value)
    {
        // Preserve all caps
        if (strtoupper($value) === $value) {
            return $value;
        }

        return Str::camel($value);
    }

    /**
     * Str::snake wrapper - for specific extra functionality
     * Note this is generally only applicable when dealing with API input/output key case
     *
     * @param  string  $value
     * @return mixed|string|string[]|null
     */
    public static function snake($value)
    {
        // Preserve all caps
        if (strtoupper($value) === $value) {
            return $value;
        }

        $value = Str::snake($value);

        // Extra things which Str::snake doesn't do, but maybe should
        $value = str_replace('-', '_', $value);
        $value = preg_replace('/__+/', '_', $value);

        return $value;
    }
}

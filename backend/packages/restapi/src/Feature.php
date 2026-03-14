<?php

namespace Packages\RestApi;

class Feature
{
    protected static $features = [];

    public static function define(string $key, $value)
    {
        self::$features[$key] = $value;
    }

    public static function active(string $key)
    {
        return array_key_exists($key, self::$features)
            ? self::$features[$key]
            : false;
    }

    public static function inactive(string $key)
    {
        return self::active($key) === false;
    }
}

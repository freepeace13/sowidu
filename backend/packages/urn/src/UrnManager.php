<?php

namespace Packages\Urn;

use Illuminate\Support\Arr;

class UrnManager
{
    protected static $prefix = 'urn:';

    protected static $separator = ':';

    protected static $namespaces = [];

    public static function register($namespace, $resource)
    {
        static::$namespaces[$namespace] = $resource;
    }

    public static function resource($namespace)
    {
        return Arr::get(static::$namespaces, $namespace);
    }

    public static function namespace($resource)
    {
        return Arr::get(array_flip(static::$namespaces), $resource);
    }

    public static function usePrefix($prefix)
    {
        static::$prefix = $prefix;
    }

    public static function useSeparator($separator)
    {
        static::$separator = $separator;
    }

    public static function parse($urn)
    {
        return static::createParserInstance()->parse($urn);
    }

    public static function resolve(string $urn)
    {
        return static::createResolverInstance()->resolve($urn);
    }

    public static function generate($resource)
    {
        return static::createGeneratorInstance()->generate($resource);
    }

    protected static function createResolverInstance()
    {
        return new UrnResolver(static::createParserInstance());
    }

    protected static function createParserInstance()
    {
        return new UrnParser(static::$prefix, static::$separator);
    }

    protected static function createGeneratorInstance()
    {
        return new UrnGenerator(static::$prefix, static::$separator);
    }
}

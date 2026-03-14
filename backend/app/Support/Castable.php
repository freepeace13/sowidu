<?php

namespace App\Support;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use JsonSerializable;

class Castable implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    protected $attributes = [];

    protected $original = [];

    protected $fillable = [];

    protected static $unguarded = false;

    protected static $mutatorCache = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);

        $this->syncOriginal();
    }

    public static function unguarded(callable $callback)
    {
        if (static::$unguarded) {
            return $callback();
        }

        static::unguard();

        try {
            return $callback();
        } finally {
            static::reguard();
        }
    }

    public static function unguard($state = true)
    {
        static::$unguarded = $state;
    }

    public static function reguard()
    {
        static::$unguarded = false;
    }

    public function fill(array $attributes)
    {
        foreach ($this->fillableFromArray($attributes) as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }
    }

    public function forceFill(array $attributes)
    {
        return static::unguarded(function () use ($attributes) {
            return $this->fill($attributes);
        });
    }

    public function fillableFromArray(array $attributes)
    {
        if (count($this->getFillable()) > 0) {
            return array_intersect_key($attributes, array_flip($this->getFillable()));
        }

        return $attributes;
    }

    public function isFillable($key)
    {
        return in_array($key, $this->getFillable());
    }

    public function getFillable()
    {
        return $this->fillable;
    }

    protected static function getMutatorMethods($class)
    {
        preg_match_all('/(?<=^|;)get([^;]+?)Attribute(;|$)/', implode(';', get_class_methods($class)), $matches);

        return $matches[1];
    }

    public static function cacheMutatedAttributes($class)
    {
        static::$mutatorCache[$class] = collect(static::getMutatorMethods($class))->map(function ($match) {
            return lcfirst($match);
        })->all();
    }

    public function getMutatedAttributes()
    {
        $class = static::class;

        if (!isset(static::$mutatorCache[$class])) {
            static::cacheMutatedAttributes($class);
        }

        return static::$mutatorCache[$class];
    }

    public function attributesToArray()
    {
        $attributes = $this->getAttributes();

        return $this->addMutatedAttributesToArray(
            $attributes, $this->getMutatedAttributes(),
        );
    }

    protected function addMutatedAttributesToArray(array $attributes, array $mutatedAttributes)
    {
        foreach ($mutatedAttributes as $key) {
            if (!array_key_exists($key, $attributes)) {
                continue;
            }

            $attributes[$key] = $this->mutateAttributeForArray($key, $attributes[$key]);
        }

        return $attributes;
    }

    protected function mutateAttributeForArray($key, $value)
    {
        $value = $this->getMutatedAttributeValue($key, $value);

        return $value instanceof Arrayable ? $value->toArray() : $value;
    }

    public function setAttribute($key, $value)
    {
        if ($this->hasSetMutator($key)) {
            return $this->setMutatedAttributeValue($key, $value);
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    public function getAttribute($key)
    {
        return $this->transformAttributeValue($key, $this->getAttributeFromArray($key));
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    protected function transformAttributeValue($key, $value)
    {
        if ($this->hasGetMutator($key)) {
            return $this->getMutatedAttributeValue($key, $value);
        }

        return $value;
    }

    protected function getAttributeFromArray($key)
    {
        return $this->getAttributes()[$key] ?? null;
    }

    public function setMutatedAttributeValue($key, $value)
    {
        return $this->{'set' . Str::studly($key) . 'Attribute'}($value);
    }

    public function getMutatedAttributeValue($key, $value)
    {
        return $this->{'get' . Str::studly($key) . 'Attribute'}($value);
    }

    public function hasSetMutator($key)
    {
        return method_exists($this, 'set' . Str::studly($key) . 'Attribute');
    }

    public function hasGetMutator($key)
    {
        return method_exists($this, 'get' . Str::studly($key) . 'Attribute');
    }

    // public function syncOriginalAttributes($attributes)
    // {
    //     $attributes = is_array($attributes) ? $attributes : func_get_args();

    //     $attributesArray = $this->getAttributes();

    //     foreach ($attributes as $attribute) {
    //         $this->original[$attribute] = $attributesArray[$attribute];
    //     }

    //     return $this;
    // }

    // public function syncOriginalAttribute($attribute)
    // {
    //     return $this->syncOriginalAttributes($attribute);
    // }

    public function syncOriginal()
    {
        $this->original = $this->getAttributes();

        return $this;
    }

    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->attributes = $attributes;

        if ($sync) {
            $this->syncOriginal();
        }

        return $this;
    }

    protected function getOriginalWithoutRewindingInstance($key = null, $default = null)
    {
        if ($key) {
            return $this->transformAttributeValue(
                $key, Arr::get($this->original, $key, $default),
            );
        }

        return collect($this->original)->mapWithKeys(function ($value, $key) {
            return [$key => $this->transformAttributeValue($key, $value)];
        })->all();
    }

    public function getOriginal($key = null, $default = null)
    {
        return (new static($this->original))->setRawAttributes(
            $this->original, $sync = true,
        )->getOriginalWithoutRewindingInstance($key, $default);
    }

    public function toArray()
    {
        return $this->attributesToArray();
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->attributes[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    public function __debugInfo()
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return $this->toJson();
    }
}

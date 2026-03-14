<?php

namespace Packages\Data\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasAttributes
{
    protected $attributes = [];

    protected $original = [];

    protected static $mutatorCache = [];

    protected function getOriginalWithoutRewindingInstance($key = null, $default = null)
    {
        if ($key) {
            return $this->transformAttributeValue(
                $key,
                Arr::get($this->original, $key, $default),
            );
        }

        return collect($this->original)->mapWithKeys(function ($value, $key) {
            return [$key => $this->transformAttributeValue($key, $value)];
        })->all();
    }

    public function getOriginal($key = null, $default = null)
    {
        return (new static($this->original))->setRawAttributes(
            $this->original,
            $sync = true,
        )->getOriginalWithoutRewindingInstance($key, $default);
    }

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
            $attributes,
            $this->getMutatedAttributes(),
        );
    }

    protected function transformAttributeValue($key, $value)
    {
        if ($this->hasGetMutator($key)) {
            return $this->getMutatedAttributeValue($key, $value);
        }

        return $value;
    }

    public function getAttribute($key)
    {
        return $this->transformAttributeValue($key, $this->getAttributeFromArray($key));
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttribute($key, $value)
    {
        if ($this->hasSetMutator($key)) {
            return $this->setMutatedAttributeValue($key, $value);
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    protected function getAttributeFromArray($key)
    {
        return $this->getAttributes()[$key] ?? null;
    }

    public function setMutatedAttributeValue($key = '', $value = '')
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
}

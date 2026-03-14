<?php

namespace Packages\Data\Concerns;

trait GuardsAttributes
{
    protected $fillable = [];

    protected static $unguarded = false;

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
        if (count($this->getFillable()) > 0 && !static::$unguarded) {
            return array_intersect_key($attributes, array_flip($this->getFillable()));
        }

        return $attributes;
    }

    public function isFillable($key)
    {
        if (static::$unguarded) {
            return true;
        }

        return in_array($key, $this->getFillable());
    }

    public function getFillable()
    {
        return $this->fillable;
    }
}

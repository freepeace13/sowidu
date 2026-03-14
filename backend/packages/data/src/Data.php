<?php

namespace Packages\Data;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

abstract class Data implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use Concerns\GuardsAttributes,
        Concerns\HasAttributes;

    public function __construct(array $attributes = [])
    {
        $this->syncOriginal();

        $this->fill($attributes);
    }

    public function toArray()
    {
        return $this->attributesToArray();
    }

    public function jsonSerialize(): array
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

    public function offsetExists($offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->attributes[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset($offset): void
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

    abstract public static function from($payload): Data;

    public static function collection($items = []): DataCollection
    {
        return (new DataCollection($items))->map(
            fn ($item) => match (true) {
                $item instanceof Data => $item,
                default => static::from($item),
            },
        );
    }
}

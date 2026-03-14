<?php

namespace Modules\Shared;

use ArrayAccess;
use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\ForwardsCalls;
use JsonSerializable;

abstract class Transformer implements ArrayAccess, JsonSerializable
{
    use ForwardsCalls;

    protected $resource;

    private $callbacks = [];

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    abstract public function toArray($request);

    public function resolve($request = null)
    {
        $data = $this->toArray(
            $request = $request ?: Container::getInstance()->make('request'),
        );

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        } elseif ($data instanceof JsonSerializable) {
            $data = $data->jsonSerialize();
        }

        return $this->filter((array) $data);
    }

    protected function filter(array $data)
    {
        $callbacks = $this->callbacks ?? [];

        foreach ($callbacks as $callback) {
            foreach ($callback($data) as $key => $value) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    protected function state(Closure $callback)
    {
        $callbacks = $this->callbacks ?? [];

        $callbacks[] = $callback;

        $this->callbacks = $callbacks;

        return $this;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->resource[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->resource[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->resource[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->resource[$offset]);
    }

    public function __isset($key)
    {
        return isset($this->resource->{$key});
    }

    public function __unset($key)
    {
        unset($this->resource->{$key});
    }

    public function __get($key)
    {
        return $this->resource->{$key};
    }

    public function jsonSerialize(): mixed
    {
        return $this->resolve(Container::getInstance()->make('request'));
    }

    public static function make(...$parameters)
    {
        return new static(...$parameters);
    }

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->resource, $method, $parameters);
    }

    public function toObject(): object
    {
        return json_decode(json_encode($this->resolve()));
    }
}

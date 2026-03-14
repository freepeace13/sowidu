<?php

namespace Packages\RestApi\Resources;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource as IlluminateJsonResource;
use Illuminate\Support\Arr;
use JsonSerializable;
use Packages\RestApi\Feature;

class JsonResource extends IlluminateJsonResource
{
    protected $callbacks = [];

    protected $transformerClass;

    public function __construct($resource)
    {
        parent::__construct($resource);

        // Check if the $transformer is defined
        if ($this->hasTransformer()) {
            $this->transformerClass = new $this->transformer($resource);
        }
    }

    protected function ignoreKeys(): array
    {
        return array_keys($this->transformerClass->toArray($this->resource));
    }

    protected function hasTransformer(): bool
    {
        return isset($this->transformer);
    }

    public function __call($name, $parameter)
    {
        // // Check if method exists on this class
        if (method_exists($this, $name)) {
            return $this->$name(...$parameter);
        }

        // Check if method exists on the transformer
        if ($this->hasTransformer() && method_exists($this->transformerClass, $name)) {
            // Call the method on the transformer not static
            $data = $this->transformerClass->$name(...$parameter)
                ->resolve();

            // Remove data on the ignoreKeys
            $data = array_diff_key($data, array_flip($this->ignoreKeys()));

            return $this->state(fn () => $data);
        }

        // Check if method exists on the resource
        if (method_exists($this->resource, $name)) {
            return $this->resource->$name(...$parameter);
        }

        return parent::__call($name, $parameter);
    }

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

        $callbacks = $this->callbacks ?? [];

        foreach ($callbacks as $callback) {
            $data = array_replace($data, $callback($data));
        }

        return $this->cleanAttributes($this->filter((array) $data), $request);
    }

    protected function cleanAttributes(array $data, $request)
    {
        if (Feature::inactive('FilteredResourceAttributes')) {
            return $data;
        }

        // The comma-separated fields to include. Dotted fields are deeper fields.
        $includeFields = $request->query('fields') ?? '';
        $includeFields = explode(',', $includeFields);

        $attributes = [];

        foreach ($includeFields as $path) {
            if (Arr::has($data, $path)) {
                Arr::set($attributes, $path, Arr::get($data, $path));
            }
        }

        if (empty($attributes)) {
            return $data;
        }

        return $attributes;
    }

    protected function state(Closure $callback)
    {
        $callbacks = $this->callbacks ?? [];

        $callbacks[] = $callback;

        $this->callbacks = $callbacks;

        return $this;
    }

    /**
     * Create a new anonymous resource collection.
     *
     * @param  mixed  $resource
     * @return \App\Http\Api\Resources\AnonymousResourceCollection
     */
    public static function collection($resource, $callback = null)
    {
        return tap(new AnonymousResourceCollection($resource, static::class, $callback), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }

    public function toResponse($request)
    {
        return (new ResourceResponse($this))->toResponse($request);
    }
}

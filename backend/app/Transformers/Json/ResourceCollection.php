<?php

namespace App\Transformers\Json;

use Illuminate\Support\Collection;

class ResourceCollection extends JsonTransformer
{
    public $collects;

    public $collection;

    public function __construct($resource, $collects)
    {
        parent::__construct($resource);

        $this->collects = $collects;
        $this->resource = $this->collectResource($resource);
    }

    protected function collectResource($resource)
    {
        if (is_array($resource)) {
            $resource = new Collection($resource);
        }

        $this->collection = $this->collects && !$resource->first() instanceof $this->collects
            ? $resource->mapInto($this->collects)
            : $resource->toBase();

        return $this->collection;
    }

    public function toArray($request)
    {
        return $this->collection->map->toArray($request)->all();
    }
}

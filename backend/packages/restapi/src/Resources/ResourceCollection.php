<?php

namespace Packages\RestApi\Resources;

use Countable;
use Illuminate\Http\Resources\CollectsResources;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use IteratorAggregate;

class ResourceCollection extends JsonResource implements Countable, IteratorAggregate
{
    use CollectsResources;

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects;

    /**
     * The mapped collection instance.
     *
     * @var \Illuminate\Support\Collection
     */
    public $collection;

    /**
     * The state callbacks
     *
     * @var mixed
     */
    protected $resourceCallback;

    /**
     * Indicates if all existing request query parameters should be added to pagination links.
     *
     * @var bool
     */
    protected $preserveAllQueryParameters = false;

    /**
     * The query parameters that should be added to the pagination links.
     *
     * @var array|null
     */
    protected $queryParameters;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource, $resourceCallback = null)
    {
        $this->resourceCallback = $resourceCallback;

        parent::__construct($resource);

        $this->resource = $this->collectResource($resource);
    }

    public function getResourceCallback()
    {
        return $this->resourceCallback ?: (function () {
            //
        });
    }

    /**
     * Map the given collection resource into its individual resources.
     *
     * @param  mixed  $resource
     * @return mixed
     */
    protected function collectResource($resource)
    {
        if ($resource instanceof MissingValue) {
            return $resource;
        }

        if (is_array($resource)) {
            $resource = new Collection($resource);
        }

        $collects = $this->collects();

        $this->collection = $collects && !$resource->first() instanceof $collects
            ? $resource->map(fn ($item) => tap(new $collects($item), $this->getResourceCallback()))
            : $resource->toBase();

        return ($resource instanceof AbstractPaginator || $resource instanceof AbstractCursorPaginator)
            ? $resource->setCollection($this->collection)
            : $this->collection;
    }

    /**
     * Indicate that all current query parameters should be appended to pagination links.
     *
     * @return $this
     */
    public function preserveQuery()
    {
        $this->preserveAllQueryParameters = true;

        return $this;
    }

    /**
     * Specify the query string parameters that should be present on pagination links.
     *
     * @return $this
     */
    public function withQuery(array $query)
    {
        $this->preserveAllQueryParameters = false;

        $this->queryParameters = $query;

        return $this;
    }

    /**
     * Return the count of items in the resource collection.
     *
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return $this->collection->count();
    }

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map->resolve($request)->all();
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        if ($this->resource instanceof AbstractPaginator || $this->resource instanceof AbstractCursorPaginator) {
            return $this->preparePaginatedResponse($request);
        }

        return parent::toResponse($request);
    }

    /**
     * Create a paginate-aware HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function preparePaginatedResponse($request)
    {
        if ($this->preserveAllQueryParameters) {
            $this->resource->appends($request->query());
        } elseif (!is_null($this->queryParameters)) {
            $this->resource->appends($this->queryParameters);
        }

        return (new PaginatedResourceResponse($this))->toResponse($request);
    }
}

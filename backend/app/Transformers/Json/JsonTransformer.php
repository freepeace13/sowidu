<?php

namespace App\Transformers\Json;

use App\Transformers\Transformer;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;

abstract class JsonTransformer extends Transformer implements Responsable
{
    public function response($request = null)
    {
        return $this->toResponse(
            $request ?: Container::getInstance()->make('request'),
        );
    }

    public function toResponse($request)
    {
        return tap(
            Response::json(
                $this->resolve($request),
                $this->calculateStatus(),
                [],
                $this->jsonOptions(),
            ),
            function ($response) {
                $response->original = $this->resource;
            },
        );
    }

    public function jsonOptions()
    {
        return 0;
    }

    public function jsonSerialize(): mixed
    {
        return $this->resolve(Container::getInstance()->make('request'));
    }

    protected function calculateStatus()
    {
        return $this->resource instanceof Model &&
            $this->resource->wasRecentlyCreated ? 201 : 200;
    }

    public static function collection($collection)
    {
        return new ResourceCollection($collection, static::class);
    }
}

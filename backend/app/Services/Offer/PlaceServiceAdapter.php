<?php

declare(strict_types=1);

namespace App\Services\Offer;

use App\Models\Place;
use App\Transformers\PlaceTransformer;
use Illuminate\Database\Eloquent\Model;
use Modules\Offer\Contracts\External\PlaceServiceContract;

class PlaceServiceAdapter implements PlaceServiceContract
{
    public function find(int $id): ?Model
    {
        return Place::find($id);
    }

    public function findOrFail(int $id): Model
    {
        return Place::findOrFail($id);
    }

    public function transform(Model $place): array
    {
        /** @var Place $place */
        return (new PlaceTransformer($place))->resolve();
    }

    public function transformForOffer(?Model $place): ?array
    {
        if (!$place) {
            return null;
        }

        /** @var Place $place */
        return (new PlaceTransformer($place))
            ->withId()
            ->withGoogleMapUrl()
            ->withShortFullAddress()
            ->resolve();
    }
}

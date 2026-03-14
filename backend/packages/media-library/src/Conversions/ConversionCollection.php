<?php

namespace Packages\MediaLibrary\Conversions;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Packages\MediaLibrary\MediaCollections\Exceptions\InvalidConversion;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class ConversionCollection extends Collection
{
    protected $media;

    public static function createForMedia(Media $media)
    {
        return (new static)->setMedia($media);
    }

    public function setMedia(Media $media): self
    {
        $this->media = $media;

        $this->items = [];

        $this->addConversionsFromRelatedModel($media);

        return $this;
    }

    public function getByName(string $name): Conversion
    {
        $conversion = $this->first(function (Conversion $conversion) use ($name) {
            return $conversion->getName() === $name;
        });

        if (!$conversion) {
            throw new InvalidConversion;
        }

        return $conversion;
    }

    protected function addConversionsFromRelatedModel(Media $media)
    {
        $modelName = Arr::get(Relation::morphMap(), $media->model_type, $media->model_type);

        $model = new $modelName;

        $model->registerMediaConversions();

        $this->items = $model->mediaConversions;
    }
}

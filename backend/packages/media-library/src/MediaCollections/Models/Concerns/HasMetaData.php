<?php

namespace Packages\MediaLibrary\MediaCollections\Models\Concerns;

use Illuminate\Support\Arr;

trait HasMetaData
{
    public $defaultSettings = [
        'metadata' => [
            'category' => null,
            'edited_by' => null,
            'edited_at' => null,
        ],
    ];

    // public static function bootHasMetaData()
    // {
    //     static::saving(function ($model) {
    //         $model->setCustomProperty('metadata', array_merge(
    //             $model->defaultSettings['metadata'],
    //             $model->getCustomProperty('metadata', [])
    //         ));
    //     });
    // }

    public function metaAll()
    {
        return array_merge(
            $this->defaultSettings['metadata'],
            $this->getCustomProperty('metadata', []),
        );
    }

    public function metaGet(string $key, $default = null)
    {
        $metadata = $this->metaAll();

        return Arr::get($this->metaAll(), $key, $default);
    }

    public function metaSet(string $key, $value = null)
    {
        $metadata = $this->metaAll();

        Arr::set($metadata, $key, $value);

        $this->setCustomProperty('metadata', $metadata)->save();

        return $this;
    }

    public function metaReset()
    {
        $this->setCustomProperty('metadata', $this->defaultSettings['metadata'])->save();

        return $this;
    }

    public function metaForget(string $key)
    {
        $metadata = $this->metaAll();

        Arr::forget($metadata, $key);

        $this->setCustomProperty('metadata', $metadata)->save();

        return $this;
    }

    public function scopeWhereMeta($query, string $key, $value = null)
    {
        return $query->where("custom_property->metadata->{$key}", $value);
    }
}

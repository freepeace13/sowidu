<?php

namespace Packages\MediaLibrary;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Packages\MediaLibrary\Conversions\Conversion;
use Packages\MediaLibrary\MediaCollections\FileAdder;
use Packages\MediaLibrary\MediaCollections\MediaRepository;
use Packages\MediaLibrary\MediaCollections\Models\Media;

trait InteractsWithMedia
{
    /** @var Conversion[] */
    public array $mediaConversions = [];

    protected array $unAttachedMediaItems = [];

    protected bool $deletePreservingMedia = false;

    public static function bootInteractsWithMedia()
    {
        static::deleting(function (HasMedia $model) {
            if ($model->shouldDeletePreservingMedia()) {
                return;
            }

            if (in_array(SoftDeletes::class, class_uses_recursive($model))) {
                if (!$model->forceDeleting) {
                    return;
                }
            }

            $model->mediaFiles()->cursor()->each(fn (Media $media) => $media->delete());
        });
    }

    public function addMedia($file): FileAdder
    {
        return app(FileAdder::class)
            ->setSubject($this)
            ->setFile($file);
    }

    public function getMedia(): MediaRepository
    {
        return app(MediaRepository::class)
            ->setSubject($this);
    }

    public function addMediaConversion(string $name): Conversion
    {
        return $this->mediaConversions[] = Conversion::create($name);
    }

    public function loadMedia(): Collection
    {
        return $this->exists
            ? $this->mediaFiles
            : collect($this->unAttachedMediaItems)->pluck('media');
    }

    public function prepareToAttachMedia(Media $media, FileAdder $fileAdder)
    {
        $this->unAttachedMediaItems[] = compact('media', 'fileAdder');
    }

    public function processUnattachedMedia(callable $callable)
    {
        foreach ($this->unAttachedMediaItems as $item) {
            $callable($item['media'], $item['fileAdder']);
        }

        $this->unAttachedMediaItems = [];
    }

    public function mediaFiles(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function deletePreservingMedia(): bool
    {
        $this->deletePreservingMedia = true;

        return $this->delete();
    }

    public function shouldDeletePreservingMedia(): bool
    {
        return $this->deletePreservingMedia ?? false;
    }

    public function __sleep(): array
    {
        // do not serialize properties from the trait
        return collect(parent::__sleep())
            ->reject(
                fn ($key) => in_array(
                    $key,
                    [
                        'mediaConversions',
                        'mediaCollections',
                        'unAttachedMediaLibraryItems',
                        'deletePreservingMedia',
                    ],
                ),
            )->toArray();
    }
}

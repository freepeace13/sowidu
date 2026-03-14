<?php

namespace Packages\MediaLibrary;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Packages\MediaLibrary\Conversions\Conversion;
use Packages\MediaLibrary\MediaCollections\FileAdder;
use Packages\MediaLibrary\MediaCollections\MediaRepository;
use Packages\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @method string getKey()
 * @method string getMorphClass()
 * @method void prepareToAttachMedia(Media $media, FileAdder $fileAdder)
 */
interface HasMedia
{
    public function loadMedia(): Collection;

    public function getMediaPathRelativeToRoot(): string;

    public function getMedia(): MediaRepository;

    public function addMedia($file): FileAdder;

    public function mediaFiles(): MorphMany;

    public function addMediaConversion(string $name): Conversion;

    public function deletePreservingMedia(): bool;

    public function shouldDeletePreservingMedia(): bool;

    public function registerMediaConversions(?Media $media = null): void;
}

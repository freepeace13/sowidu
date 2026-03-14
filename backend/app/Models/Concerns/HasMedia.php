<?php

namespace App\Models\Concerns;

use Packages\MediaLibrary\InteractsWithMedia;
use Packages\MediaLibrary\MediaCollections\Models\Media;

trait HasMedia
{
    use InteractsWithMedia;

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->keepOriginalImageFormat()
            ->extractVideoFrameAtSecond(5)
            ->pdfPageNumber(1)
            ->width(368)
            ->height(232);
    }
}

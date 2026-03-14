<?php

namespace Packages\MediaLibrary\Support;

use Packages\MediaLibrary\MediaCollections\Models\Media;

class PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    public function getBasePath(Media $media): string
    {
        $prefix = $media->loadMissing('model')->model->getMediaPathRelativeToRoot();

        return $prefix . '/' . $media->uuid;
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive-images/';
    }
}

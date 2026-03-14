<?php

namespace Packages\MediaLibrary\ResponsiveImages\Events;

use Illuminate\Queue\SerializesModels;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class ResponsiveImagesGenerated
{
    use SerializesModels;

    public function __construct(public Media $media) {}
}

<?php

namespace Packages\MediaLibrary\Conversions\ImageGenerators;

use Illuminate\Support\Collection;
use Packages\MediaLibrary\Conversions\Conversion;

class Image extends ImageGenerator
{
    public function convert(string $path, ?Conversion $conversion = null): string
    {
        return $path;
    }

    public function requirementsAreInstalled(): bool
    {
        return true;
    }

    public function supportedExtensions(): Collection
    {
        return new Collection(['png', 'jpg', 'jpeg', 'gif']);
    }

    public function supportedMimeTypes(): Collection
    {
        return new Collection(config('media-library.mime_types.images'));
    }
}

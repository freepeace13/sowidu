<?php

namespace Packages\MediaLibrary\Support;

use Packages\MediaLibrary\MediaCollections\Models\Media;

class FileNamer
{
    public function originalFileName(string $fileName): string
    {
        return pathinfo($fileName, PATHINFO_FILENAME);
    }

    public function conversionFileName(string $fileName, $conversion): string
    {
        $strippedFileName = pathinfo($fileName, PATHINFO_FILENAME);

        return "{$strippedFileName}-{$conversion->getName()}";
    }

    public function responsiveFileName(string $fileName): string
    {
        return pathinfo($fileName, PATHINFO_FILENAME);
    }

    public function temporaryFileName(Media $media, string $extension): string
    {
        return "{$this->responsiveFileName($media->file_name)}.{$extension}";
    }

    public function extensionFromBaseImage(string $baseImage): string
    {
        return pathinfo($baseImage, PATHINFO_EXTENSION);
    }
}

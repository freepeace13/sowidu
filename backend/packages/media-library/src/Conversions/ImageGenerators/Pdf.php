<?php

namespace Packages\MediaLibrary\Conversions\ImageGenerators;

use Illuminate\Support\Collection;
use Packages\MediaLibrary\Conversions\Conversion;

class Pdf extends ImageGenerator
{
    public function convert(string $file, ?Conversion $conversion = null): string
    {
        $imageFile = pathinfo($file, PATHINFO_DIRNAME) . '/' . pathinfo($file, PATHINFO_FILENAME) . '.jpg';

        $pageNumber = $conversion ? $conversion->getPdfPageNumber() : 1;

        (new \Spatie\PdfToImage\Pdf($file))->setPage($pageNumber)->saveImage($imageFile);

        return $imageFile;
    }

    public function requirementsAreInstalled(): bool
    {
        if (!class_exists(\Imagick::class)) {
            return false;
        }

        if (!class_exists(\Spatie\PdfToImage\Pdf::class)) {
            return false;
        }

        return true;
    }

    public function supportedExtensions(): Collection
    {
        return new Collection(['pdf']);
    }

    public function supportedMimeTypes(): Collection
    {
        return new Collection(config('media-library.mime_types.documents'));
    }
}

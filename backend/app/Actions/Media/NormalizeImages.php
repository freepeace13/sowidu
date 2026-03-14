<?php

namespace App\Actions\Media;

use Illuminate\Http\UploadedFile;
use Maestroerror\HeicToJpg;
use Packages\MediaLibrary\Support\File;

class NormalizeImages
{
    public function normalize($file, $extension = 'jpg')
    {
        if (is_string($file)) {
            $path = $file;
        }

        if ($file instanceof UploadedFile) {
            $path = $file->getPath() . '/' . $file->getFilename();
        }

        if (self::isConversionNeeded($path)) {
            $destination = $this->toImage($path, $extension);

            return new UploadedFile(
                $destination,
                pathinfo($destination, PATHINFO_BASENAME),
                File::getMimeType($destination),
            );
        }

        return $file;
    }

    protected function toImage($path, $extension = 'jpg')
    {
        $targetDirectory = pathinfo($path, PATHINFO_DIRNAME);
        $targetFileName = pathinfo($path, PATHINFO_FILENAME) . ".{$extension}";

        HeicToJpg::convert($path)->saveAs(
            $destination = $targetDirectory . DIRECTORY_SEPARATOR . $targetFileName,
        );

        unlink($path);

        return $destination;
    }

    protected static function isConversionNeeded($path): bool
    {
        return in_array(File::getMimeType($path), [
            'image/heic',
            'image/heif',
        ]);
    }
}

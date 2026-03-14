<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\MediaLibrary\Support\PathGenerator;

/** @todo add authorization check */
class FileController extends Controller
{
    public function serve(PathGenerator $pathGenerator, string $media, string $path)
    {
        $media = Media::withTrashed()->whereUuid($media)
            ->first();

        abort_if(!$media, 404, 'Not Found.');

        $disk = Storage::disk($media->disk);

        $prefix = $pathGenerator->getBasePath($media);
        $filePath = $disk->path($prefix . DIRECTORY_SEPARATOR . $path);

        ob_end_clean();

        return response()->file($filePath);
    }

    public function download(string $media)
    {
        $media = Media::whereUuid($media)->first();

        abort_if(!$media, 404, 'Not Found.');

        $response = response()->download(
            \Storage::disk('media')->path($media->getPath()),
        );

        ob_end_clean();

        return $response;
    }
}

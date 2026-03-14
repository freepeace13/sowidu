<?php

namespace App\Actions\Media;

use Maestroerror\HeicToJpg;
use Packages\MediaLibrary\Conversions\Actions\PerformRegenerationAction;
use Packages\MediaLibrary\MediaCollections\Filesystem;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\MediaLibrary\Support\File;
use Packages\MediaLibrary\Support\FileNamer;
use Packages\MediaLibrary\Support\PathGenerator;

class ConvertHeicToJpg
{
    protected $filesystem;

    protected $pathGenerator;

    protected $fileNamer;

    public function __construct(Filesystem $filesystem, PathGenerator $pathGenerator, FileNamer $fileNamer)
    {
        $this->filesystem = $filesystem;
        $this->pathGenerator = $pathGenerator;
        $this->fileNamer = $fileNamer;
    }

    public function convert(Media $media)
    {
        if (!HeicToJpg::isHeic($media->getPath())) {
            return;
        }

        $originalPath = $media->getPath();
        $originalFileName = $this->fileNamer->originalFileName($originalPath);

        $newFileName = $originalFileName . '.jpg';
        $newFilePath = $this->pathGenerator->getBasePath($media) . $newFileName;

        HeicToJpg::convert($originalPath)->saveAs($newFilePath);

        $media->forceFill([
            'file_name' => $newFileName,
            'mime_type' => File::getMimeType($newFilePath),
            'size' => filesize($newFilePath),
        ])->save();

        (new PerformRegenerationAction)->execute(
            media: $media,
            withResponsiveImages: true,
        );

        unlink($originalPath);
    }
}

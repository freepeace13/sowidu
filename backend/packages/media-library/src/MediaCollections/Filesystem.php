<?php

namespace Packages\MediaLibrary\MediaCollections;

use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Support\Str;
use Packages\MediaLibrary\Conversions\ConversionCollection;
use Packages\MediaLibrary\Conversions\FileManipulator;
use Packages\MediaLibrary\MediaCollections\Exceptions\DiskCannotBeAccessed;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\MediaLibrary\Support\File;
use Packages\MediaLibrary\Support\PathGenerator;

class Filesystem
{
    protected $diskName;

    protected $filesystem;

    protected array $customRemoteHeaders = [];

    public function __construct(Factory $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function add(string $file, Media $media, ?string $targetFileName = null)
    {
        $this->copyToMedia($file, $media, null, $targetFileName);

        app(FileManipulator::class)->createDerivedFiles($media);

        return true;
    }

    public function getStream(Media $media)
    {
        $sourceFile = $this->getMediaDirectory($media) . '/' . $media->file_name;

        return $this->filesystem->disk($media->disk)->readStream($sourceFile);
    }

    public function copyFromMedia(Media $media, string $targetFile): string
    {
        file_put_contents($targetFile, $this->getStream($media));

        return $targetFile;
    }

    public function removeAllFiles(Media $media)
    {
        $directory = $this->getMediaDirectory($media);

        $this->filesystem->disk($media->disk)->deleteDirectory($directory);
    }

    public function removeFile(Media $media, string $path)
    {
        $disk = $this->filesystem->disk($media->disk);

        if ($disk->exists($path) && !is_dir($path)) {
            $disk->delete($path);
        }
    }

    public function syncFileNames(Media $media)
    {
        $this->renameMediaFile($media);
        $this->renameConversionFiles($media);
    }

    protected function renameMediaFile(Media $media)
    {
        $newFileName = $media->file_name;
        $oldFileName = $media->getOriginal('file_name');

        $mediaDirectory = $this->getMediaDirectory($media);

        $oldFile = "{$mediaDirectory}/{$oldFileName}";
        $newFile = "{$mediaDirectory}/{$newFileName}";

        $this->filesystem->disk($media->disk)->move($oldFile, $newFile);
    }

    protected function renameConversionFiles(Media $media)
    {
        $mediaWithOldFileName = Media::find($media->id);
        $mediaWithOldFileName->file_name = $mediaWithOldFileName->getOriginal('file_name');

        $conversionDirectory = $this->getConversionDirectory($media);

        $conversionCollection = ConversionCollection::createForMedia($media);

        foreach ($media->getConversionNames() as $conversionName) {
            $conversion = $conversionCollection->getByName($conversionName);

            $oldFile = $conversionDirectory . $conversion->getConversionFile($mediaWithOldFileName);
            $newFile = $conversionDirectory . $conversion->getConversionFile($media);

            $disk = $this->filesystem->disk($media->disk);

            // A media conversion file might be missing, waiting to be generated, failed etc.
            if (!$disk->exists($oldFile)) {
                continue;
            }

            $disk->move($oldFile, $newFile);
        }
    }

    public function copyToMedia(string $pathToFile, Media $media, ?string $type = null, ?string $targetFileName = null)
    {
        $destinationFileName = $targetFileName ?: pathinfo($pathToFile, PATHINFO_BASENAME);

        $destination = $this->getMediaDirectory($media, $type) . $destinationFileName;

        $file = fopen($pathToFile, 'r');

        $diskDriverName = $media->getDiskDriverName();

        if ($diskDriverName === 'local') {
            $success = $this->filesystem
                ->disk($media->disk)
                ->put($destination, $file);

            fclose($file);

            if (!$success) {
                throw DiskCannotBeAccessed::create($media->disk);
            }

            return;
        }

        $success = $this->filesystem
            ->disk($media->disk)
            ->put(
                $destination,
                $file,
                $this->getRemoteHeadersForFile($pathToFile, $media->getCustomHeaders()),
            );

        if (is_resource($file)) {
            fclose($file);
        }

        if (!$success) {
            throw DiskCannotBeAccessed::create($media->disk);
        }
    }

    public function getRemoteHeadersForFile(
        string $file,
        array $mediaCustomHeaders = [],
        ?string $mimeType = null,
    ): array {
        $mimeTypeHeader = ['ContentType' => $mimeType ?: File::getMimeType($file)];

        $extraHeaders = config('media-library.remote.extra_headers');

        return array_merge(
            $mimeTypeHeader,
            $extraHeaders,
            $this->customRemoteHeaders,
            $mediaCustomHeaders,
        );
    }

    public function removeResponsiveImages(Media $media, string $conversionName = 'media_library_original'): void
    {
        $responsiveImagesDirectory = $this->getResponsiveImagesDirectory($media);

        $allFilePaths = $this->filesystem->disk($media->disk)->allFiles($responsiveImagesDirectory);

        $responsiveImagePaths = array_filter(
            $allFilePaths,
            fn (string $path) => Str::contains($path, $conversionName),
        );

        $this->filesystem->disk($media->disk)->delete($responsiveImagePaths);
    }

    public function getMediaDirectory(Media $media, ?string $type = null): string
    {
        $pathGenerator = app(PathGenerator::class);

        if (is_null($type)) {
            $directory = $pathGenerator->getPath($media);
        }

        if ($type === 'conversions') {
            $directory = $pathGenerator->getPathForConversions($media);
        }

        if ($type === 'responsiveImages') {
            $directory = $pathGenerator->getPathForResponsiveImages($media);
        }

        if (!in_array($media->getDiskDriverName(), ['s3'], true)) {
            $this->filesystem
                ->disk($media->disk)
                ->makeDirectory($directory);
        }

        return $directory;
    }

    public function getConversionDirectory(Media $media)
    {
        return $this->getMediaDirectory($media, 'conversions');
    }

    public function getResponsiveImagesDirectory(Media $media): string
    {
        return $this->getMediaDirectory($media, 'responsiveImages');
    }

    public function determineDiskName(Media $media)
    {
        return $this->diskName ?: $media->disk ?: config('media-library.disk_name');
    }
}

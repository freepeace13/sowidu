<?php

namespace Packages\MediaLibrary\Conversions;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Packages\MediaLibrary\Conversions\Actions\PerformConversionAction;
use Packages\MediaLibrary\Conversions\ImageGenerators\ImageGeneratorFactory;
use Packages\MediaLibrary\Conversions\Jobs\PerformConversionsJob;
use Packages\MediaLibrary\MediaCollections\Filesystem;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\MediaLibrary\ResponsiveImages\Jobs\GenerateResponsiveImagesJob;
use Packages\MediaLibrary\Support\TemporaryDirectory;

class FileManipulator
{
    public function createDerivedFiles(Media $media, array $onlyConversionNames = [], bool $withResponsiveImages = false)
    {
        if (!$this->canConvertMedia($media)) {
            return;
        }

        [$queuedConversions, $conversions] = ConversionCollection::createForMedia($media)
            ->filter(function (Conversion $conversion) use ($onlyConversionNames) {
                if (count($onlyConversionNames) === 0) {
                    return true;
                }

                return in_array($conversion->getName(), $onlyConversionNames);
            })
            ->partition(function (Conversion $conversion) {
                return $conversion->shouldBeQueued();
            });

        $this
            ->performConversions($media, $conversions)
            ->dispatchQueuedConversions($media, $queuedConversions)
            ->generateResponsiveImages($media, $withResponsiveImages);
    }

    public function performConversions(
        Media $media,
        ConversionCollection $conversions,
    ): self {
        if ($conversions->isEmpty()) {
            return $this;
        }

        $temporaryDirectory = TemporaryDirectory::create();

        $copiedOriginalFile = app(Filesystem::class)->copyFromMedia(
            $media,
            $temporaryDirectory->path(Str::random(32) . '.' . $media->extension),
        );

        $conversions
            ->reject(function (Conversion $conversion) use ($media) {
                $relativePath = $media->getPath($conversion->getName());

                if ($rootPath = config("filesystem.disks.{$media->disk}.root")) {
                    $relativePath = str_replace($rootPath, '', $relativePath);
                }

                return Storage::disk($media->disk)->exists($relativePath);
            })
            ->each(function (Conversion $conversion) use ($media, $copiedOriginalFile) {
                (new PerformConversionAction)->execute($conversion, $media, $copiedOriginalFile);
            });

        $temporaryDirectory->delete();

        return $this;
    }

    protected function generateResponsiveImages(Media $media, bool $withResponsiveImages): self
    {
        if (!$withResponsiveImages) {
            return $this;
        }

        $job = (new GenerateResponsiveImagesJob($media))
            ->onConnection(config('media-library.queue_connection_name'))
            ->onQueue(config('media-library.queue_name'));

        dispatch($job);

        return $this;
    }

    protected function dispatchQueuedConversions(
        Media $media,
        ConversionCollection $conversions,
    ): self {
        if ($conversions->isEmpty()) {
            return $this;
        }

        dispatch(new PerformConversionsJob($conversions, $media));

        return $this;
    }

    protected function canConvertMedia(Media $media): bool
    {
        $imageGenerator = ImageGeneratorFactory::forMedia($media);

        return $imageGenerator ? true : false;
    }
}

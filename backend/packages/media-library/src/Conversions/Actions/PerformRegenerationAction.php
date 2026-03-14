<?php

namespace Packages\MediaLibrary\Conversions\Actions;

use Packages\MediaLibrary\Conversions\Conversion;
use Packages\MediaLibrary\Conversions\ConversionCollection;
use Packages\MediaLibrary\Conversions\FileManipulator;
use Packages\MediaLibrary\MediaCollections\Filesystem;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class PerformRegenerationAction
{
    protected $filesystem;

    protected $fileManipulator;

    public function execute(Media $media, array $onlyConversionNames = [], bool $withResponsiveImages = false)
    {
        $this->filesystem = app(Filesystem::class);
        $this->fileManipulator = app(FileManipulator::class);

        $this->removeConversionFiles($media, $onlyConversionNames);

        $this->fileManipulator->createDerivedFiles($media, $onlyConversionNames, $withResponsiveImages);
    }

    protected function removeConversionFiles(Media $media, array $onlyConversionNames = [])
    {
        $conversions = ConversionCollection::createForMedia($media);

        $conversions
            ->filter(function ($conversion) use ($onlyConversionNames) {
                if (count($onlyConversionNames) === 0) {
                    return true;
                }

                return in_array($conversion->getName(), $onlyConversionNames);
            })
            ->each(function (Conversion $conversion) use ($media) {
                $conversionFile = $this->getConversionFile($media, $conversion);

                $this->filesystem->removeFile($media, $conversionFile);

                $media->markAsConversionNotGenerated($conversion->getName());
            });
    }

    protected function getConversionFile(Media $media, Conversion $conversion)
    {
        $directory = $this->filesystem->getConversionDirectory($media);

        return $directory . $conversion->getConversionFile($media);
    }
}

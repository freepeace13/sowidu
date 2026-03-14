<?php

namespace Packages\MediaLibrary\Conversions\Actions;

use Packages\MediaLibrary\Conversions\Conversion;
use Packages\MediaLibrary\Conversions\ImageGenerators\ImageGeneratorFactory;
use Packages\MediaLibrary\MediaCollections\Filesystem;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class PerformConversionAction
{
    public function execute(
        Conversion $conversion,
        Media $media,
        string $copiedOriginalFile,
    ) {
        $imageGenerator = ImageGeneratorFactory::forMedia($media);

        $copiedOriginalFile = $imageGenerator->convert($copiedOriginalFile, $conversion);

        $manipulationResult = (new PerformManipulationsAction)->execute($media, $conversion, $copiedOriginalFile);

        $newFileName = $conversion->getConversionFile($media);

        $renamedFile = $this->renameInLocalDirectory($manipulationResult, $newFileName);

        app(Filesystem::class)->copyToMedia($renamedFile, $media, 'conversions');

        $media->markAsConversionGenerated($conversion->getName());
    }

    protected function renameInLocalDirectory(
        string $fileNameWithDirectory,
        string $newFileNameWithoutDirectory,
    ): string {
        $targetFile = pathinfo($fileNameWithDirectory, PATHINFO_DIRNAME) . '/' . $newFileNameWithoutDirectory;

        rename($fileNameWithDirectory, $targetFile);

        return $targetFile;
    }
}

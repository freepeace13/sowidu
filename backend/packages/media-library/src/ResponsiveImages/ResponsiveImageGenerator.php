<?php

namespace Packages\MediaLibrary\ResponsiveImages;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Packages\MediaLibrary\Conversions\Conversion;
use Packages\MediaLibrary\MediaCollections\Filesystem;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\MediaLibrary\ResponsiveImages\Events\ResponsiveImagesGenerated;
use Packages\MediaLibrary\ResponsiveImages\Exceptions\InvalidTinyJpg;
use Packages\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\TinyPlaceholderGenerator;
use Packages\MediaLibrary\ResponsiveImages\WidthCalculator\WidthCalculator;
use Packages\MediaLibrary\Support\File;
use Packages\MediaLibrary\Support\FileNamer;
use Packages\MediaLibrary\Support\ImageFactory;
use Packages\MediaLibrary\Support\TemporaryDirectory;
use Spatie\TemporaryDirectory\TemporaryDirectory as BaseTemporaryDirectory;

class ResponsiveImageGenerator
{
    protected const DEFAULT_CONVERSION_QUALITY = 90;

    protected FileNamer $fileNamer;

    public function __construct(
        protected Filesystem $filesystem,
        protected WidthCalculator $widthCalculator,
        protected TinyPlaceholderGenerator $tinyPlaceholderGenerator,
    ) {
        $this->fileNamer = app(FileNamer::class);
    }

    public function generateResponsiveImages(Media $media): void
    {
        $temporaryDirectory = TemporaryDirectory::create();

        $baseImage = app(Filesystem::class)->copyFromMedia(
            $media,
            $temporaryDirectory->path(Str::random(16) . '.' . $media->extension),
        );

        if (!$this->isValidImageFile($baseImage)) {
            Log::warning('ResponsiveImageGenerator: Skipping responsive image generation for invalid or empty image file', [
                'media_id' => $media->id,
                'path' => $baseImage,
            ]);
            $temporaryDirectory->delete();

            return;
        }

        $media = $this->cleanResponsiveImages($media);

        foreach ($this->widthCalculator->calculateWidthsFromFile($baseImage) as $width) {
            $this->generateResponsiveImage($media, $baseImage, 'media_library_original', $width, $temporaryDirectory);
        }

        event(new ResponsiveImagesGenerated($media));

        $this->generateTinyJpg($media, $baseImage, 'media_library_original', $temporaryDirectory);

        $temporaryDirectory->delete();
    }

    public function generateResponsiveImagesForConversion(Media $media, Conversion $conversion, string $baseImage): void
    {
        if (!$this->isValidImageFile($baseImage)) {
            Log::warning('ResponsiveImageGenerator: Skipping responsive image generation for conversion with invalid or empty image file', [
                'media_id' => $media->id,
                'conversion' => $conversion->getName(),
                'path' => $baseImage,
            ]);

            return;
        }

        $temporaryDirectory = TemporaryDirectory::create();

        $media = $this->cleanResponsiveImages($media, $conversion->getName());

        foreach ($this->widthCalculator->calculateWidthsFromFile($baseImage) as $width) {
            $this->generateResponsiveImage($media, $baseImage, $conversion->getName(), $width, $temporaryDirectory, $this->getConversionQuality($conversion));
        }

        $this->generateTinyJpg($media, $baseImage, $conversion->getName(), $temporaryDirectory);

        $temporaryDirectory->delete();
    }

    private function getConversionQuality(Conversion $conversion): int
    {
        return $conversion->getManipulations()->getManipulationArgument('quality') ?: self::DEFAULT_CONVERSION_QUALITY;
    }

    public function generateResponsiveImage(
        Media $media,
        string $baseImage,
        string $conversionName,
        int $targetWidth,
        BaseTemporaryDirectory $temporaryDirectory,
        int $conversionQuality = self::DEFAULT_CONVERSION_QUALITY,
    ): void {
        $extension = $this->fileNamer->extensionFromBaseImage($baseImage);
        $responsiveImagePath = $this->fileNamer->temporaryFileName($media, $extension);

        $tempDestination = $temporaryDirectory->path($responsiveImagePath);

        ImageFactory::load($baseImage)
            ->optimize()
            ->width($targetWidth)
            ->quality($conversionQuality)
            ->save($tempDestination);

        $responsiveImageHeight = ImageFactory::load($tempDestination)->getHeight();

        // Users can customize the name like they want, but we expect the last part in a certain format
        $fileName = $this->addPropertiesToFileName(
            $responsiveImagePath,
            $conversionName,
            $targetWidth,
            $responsiveImageHeight,
            $extension,
        );

        $responsiveImagePath = $temporaryDirectory->path($fileName);

        rename($tempDestination, $responsiveImagePath);

        $this->filesystem->copyToMedia($responsiveImagePath, $media, 'responsiveImages');

        ResponsiveImage::register($media, $fileName, $conversionName);
    }

    public function generateTinyJpg(
        Media $media,
        string $originalImagePath,
        string $conversionName,
        BaseTemporaryDirectory $temporaryDirectory,
    ): void {
        $tempDestination = $temporaryDirectory->path('tiny.jpg');

        $this->tinyPlaceholderGenerator->generateTinyPlaceholder($originalImagePath, $tempDestination);

        $this->guardAgainstInvalidTinyPlaceHolder($tempDestination);

        $tinyImageDataBase64 = base64_encode(file_get_contents($tempDestination));

        $tinyImageBase64 = 'data:image/jpeg;base64,' . $tinyImageDataBase64;

        $originalImage = ImageFactory::load($originalImagePath);

        $originalImageWidth = $originalImage->getWidth();

        $originalImageHeight = $originalImage->getHeight();

        $svg = <<<HTML
            <!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" x="0"
            y="0" viewBox="0 0 $originalImageWidth $originalImageHeight">
                <image width="$originalImageWidth" height="$originalImageHeight" xlink:href="'.$tinyImageBase64.'">
                </image>
            </svg>
            HTML;

        $base64Svg = 'data:image/svg+xml;base64,' . base64_encode($svg);

        ResponsiveImage::registerTinySvg($media, $base64Svg, $conversionName);
    }

    protected function appendToFileName(string $filePath, string $suffix, ?string $extensionFilePath = null): string
    {
        $baseName = pathinfo($filePath, PATHINFO_FILENAME);

        $extension = pathinfo($extensionFilePath ?? $filePath, PATHINFO_EXTENSION);

        return "{$baseName}{$suffix}.{$extension}";
    }

    protected function guardAgainstInvalidTinyPlaceHolder(string $tinyPlaceholderPath): void
    {
        if (!file_exists($tinyPlaceholderPath)) {
            throw InvalidTinyJpg::doesNotExist($tinyPlaceholderPath);
        }

        if (File::getMimeType($tinyPlaceholderPath) !== 'image/jpeg') {
            throw InvalidTinyJpg::hasWrongMimeType($tinyPlaceholderPath);
        }
    }

    protected function cleanResponsiveImages(Media $media, string $conversionName = 'media_library_original'): Media
    {
        $responsiveImages = $media->responsive_images ?? [];
        $responsiveImages[$conversionName]['urls'] = [];
        $media->responsive_images = $responsiveImages;

        $this->filesystem->removeResponsiveImages($media, $conversionName);

        return $media;
    }

    protected function addPropertiesToFileName(string $fileName, string $conversionName, int $width, int $height, string $extension): string
    {
        $fileName = pathinfo($fileName, PATHINFO_FILENAME);

        return "{$fileName}___{$conversionName}_{$width}_{$height}.{$extension}";
    }

    protected function isValidImageFile(string $path): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        $fileSize = filesize($path);
        if ($fileSize === false || $fileSize === 0) {
            return false;
        }

        $mimeType = File::getMimeType($path);

        $supportedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/bmp',
        ];

        return in_array($mimeType, $supportedMimeTypes, true);
    }
}

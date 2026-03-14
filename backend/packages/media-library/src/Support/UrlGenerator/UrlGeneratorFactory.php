<?php

namespace Packages\MediaLibrary\Support\UrlGenerator;

use Packages\MediaLibrary\Conversions\ConversionCollection;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\MediaLibrary\Support\PathGenerator;

class UrlGeneratorFactory
{
    public static function createForMedia(Media $media, string $conversionName = ''): UrlGenerator
    {
        $urlGenerator = app(UrlGenerator::class);

        $pathGenerator = app(PathGenerator::class);

        $urlGenerator
            ->setMedia($media)
            ->setPathGenerator($pathGenerator);

        if ($conversionName !== '') {
            $conversion = ConversionCollection::createForMedia($media)->getByName($conversionName);

            $urlGenerator->setConversion($conversion);
        }

        return $urlGenerator;
    }
}

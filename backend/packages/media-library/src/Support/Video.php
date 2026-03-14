<?php

namespace Packages\MediaLibrary\Support;

class Video
{
    const SD = '480p';
    const HD = '720p';
    const FHD = '1080p';
    const MP4 = 'mp4';
    const WMV = 'wmv';
    const WEBM = 'webm';

    public static function getAspectRatio($quality)
    {
        return [
            self::SD => '4:3',
            self::HD => '16:9',
            self::FHD => '16:9',
        ][$quality];
    }

    public static function getDimension($quality)
    {
        return [
            self::SD => [854, 480],
            self::HD => [1280, 720],
            self::FHD => [1920, 1080],
        ][$quality];
    }
}

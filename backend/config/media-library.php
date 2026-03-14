<?php

return [
    'disk_name' => env('MEDIA_DISK', 'media'),

    // 'image_broken_placeholder_url' => '/storage/image-broken.jpg',

    'version_urls' => true,

    'image_optimizers' => [
        Spatie\ImageOptimizer\Optimizers\Jpegoptim::class => [
            '-m85', // set maximum quality to 85%
            '--strip-all', // this strips out all text information such as comments and EXIF data
            '--all-progressive', // this will make sure the resulting image is a progressive one
        ],
        Spatie\ImageOptimizer\Optimizers\Pngquant::class => [
            '--force', // required parameter for this package
        ],
    ],

    'image_generators' => [
        Packages\MediaLibrary\Conversions\ImageGenerators\Image::class,
        Packages\MediaLibrary\Conversions\ImageGenerators\Video::class,
        Packages\MediaLibrary\Conversions\ImageGenerators\Pdf::class,
    ],

    'temporary_directory_path' => storage_path('app/media-library/temp'),

    'image_driver' => env('IMAGE_DRIVER', 'gd'),

    'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
    'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),

    'queue_connection_name' => env('QUEUE_DRIVER', 'sync'),

    'queue_name' => 'default',

    'audio_codec' => env('AUDIO_CODEC', 'libvo_aacenc'),

    'transcode_mime_types' => [
        'video/quicktime',
    ],

    'responsive_images' => [
        'use_tine_placeholders' => true,
    ],

    'remote' => [
        'extra_headers' => [
            'CacheControl' => 'max-age=604800',
        ],
    ],

    'mime_types' => [
        'documents' => ['application/pdf'],
        'videos' => ['video/webm', 'video/mpeg', 'video/mp4', 'video/quicktime'],
        'images' => ['image/jpg', 'image/png', 'image/jpeg', 'image/gif'],
    ],
];

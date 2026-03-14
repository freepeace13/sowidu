<?php

return [
    'mimetypes' => [
        'videos' => [
            'video/mpeg',
            'video/x-msvideo',
            'video/3gpp',
            'video/mp4',
            'video/quicktime',
            'video/webm',
        ],

        'images' => [
            'image/svg+xml',
            'image/jpeg',
            'image/png',
        ],

        'documents' => [
            'application/pdf',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | FFProbe binary path
    |--------------------------------------------------------------------------
    */
    'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),

    /*
    |--------------------------------------------------------------------------
    | FFMpeg binary path
    |--------------------------------------------------------------------------
    */
    'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
];

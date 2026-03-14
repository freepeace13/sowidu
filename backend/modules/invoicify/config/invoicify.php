<?php

return [
    'domain' => '',

    'prefix' => 'invoicify',

    'middleware' => ['web'],

    'image_placeholder' => '',

    'mpdf' => [
        'mode' => 's',

        'format' => 'A4',

        'dpi' => env('INVOICIFY_MPDF_DPI', 96),

        'debug' => env('INVOICIFY_MPDF_DEBUG', false),

        'allow_output_buffering' => env('INVOICIFY_MPDF_OUTPUT_BUFFERING', true),

        'default_font' => 'dejavusans',

        'simpleTables' => env('INVOICIFY_MPDF_SIMPLE_TABLES', true),

        'pdfa' => env('INVOICIFY_MPDF_PDFA', true),

        'pdfaauto' => env('INVOICIFY_MPDF_PDFA_AUTO', true),

        'pdfaversion' => env('INVOICIFY_MPDF_PDFA_VERSION', '3-B'),

        'temp_dir' => storage_path('app/mpdf/tmp'),

        'cache_dir' => storage_path('app/mpdf/cache'),

        'font_dir' => storage_path('app/mpdf/fonts'),
    ],
];

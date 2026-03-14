<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Model Class Mappings
    |--------------------------------------------------------------------------
    |
    | These settings define the model classes used by the Offer module.
    | This allows the module to remain decoupled from specific implementations.
    |
    */
    'models' => [
        'user' => \App\Models\User::class,
        'company' => \App\Models\Company::class,
        'order' => \App\Models\Order::class,
        'place' => \App\Models\Place::class,
        'addressbook' => \App\Models\Addressbook::class,
        'deduction_manual' => \App\Models\DeductionManual::class,
        'employee' => \App\Models\Employee::class,
    ],

    'mpdf' => [
        'mode' => 'utf-8',

        'format' => 'A4',

        'dpi' => env('OFFER_MPDF_DPI', 96),

        'debug' => env('OFFER_MPDF_DEBUG', false),

        'allow_output_buffering' => env('OFFER_MPDF_OUTPUT_BUFFERING', false),

        'default_font' => 'dejavusans',

        'simpleTables' => env('OFFER_MPDF_SIMPLE_TABLES', true),

        // PDF/A disabled to reduce memory usage
        'pdfa' => env('OFFER_MPDF_PDFA', false),

        'pdfaauto' => env('OFFER_MPDF_PDFA_AUTO', false),

        'pdfaversion' => env('OFFER_MPDF_PDFA_VERSION', '3-B'),

        'temp_dir' => storage_path('app/mpdf/tmp'),

        'cache_dir' => storage_path('app/mpdf/cache'),

        'font_dir' => storage_path('app/mpdf/fonts'),

        'title' => '',

        'author' => '',

        'subject' => '',

        'keywords' => '',

        'creator' => '',
    ],
];

<?php

return [
    'model' => Packages\Translation\LanguageLine::class,

    'session_key' => 'translation.locale',

    'locales' => [
        'en' => 'English',
        'de' => 'German/Deutsch',
    ],

    'loaders' => [
        Packages\Translation\Loaders\DatabaseLoader::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | This files will be excluded on the default translations
    |--------------------------------------------------------------------------
    */
    'exclude' => [
        // @todo uncomment below and make it separate for each middleware
        'addressbook',
        'account',
        'order',
        'work_log',
        'catalog',
        'app_settings',
        'delivery_tickets',
        'invoices',
        'offer',
        // 'todo',
        // 'media',
        // 'chat',
    ],
];

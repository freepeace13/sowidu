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
];

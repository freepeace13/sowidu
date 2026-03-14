<?php

return [
    'length' => 6,

    'max_tries' => 3,

    'http' => [
        'path' => 'auth',

        'access_form_view' => 'index',

        'input_keys' => [
            'token' => 'token',
            'pincode' => 'pincode',
        ],
    ],

    'database' => [
        'expires' => 60 * 24 * 5, // 7 days

        'table_name' => 'magic_tokens',
    ],
];

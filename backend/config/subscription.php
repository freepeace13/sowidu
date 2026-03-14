<?php

return [
    'guards' => [
        'personal' => 'regular',
        'commercial' => 'premium',
    ],

    'packages' => [
        'premium' => [
            'task',
            'delivery',
            'order',
            'contact',
            'product',
            'media',
            'employment',
        ],

        'regular' => [
            'task',
            'delivery',
            'order',
            'contact',
            'product',
            'media',
        ],
    ],
];

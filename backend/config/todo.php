<?php

return [
    'board' => [
        'roles' => ['owner', 'co-owner', 'guest'],

        'defaults' => [
            'logo' => '/storage/board-logo.png',

            'permissions' => [
                'members' => [
                    'can_manage_task' => true,
                    'can_comment' => true,
                    'can_manage_subscriber' => false,
                    'can_manage_group' => false,
                    'can_manage_label' => false,
                ],
            ],
        ],

        'predefined' => [
            'documents' => [
                'title' => 'Documents',
                'settings' => [
                    'icon' => 'description',
                    'icon_color' => 'green darken-2',
                ],
            ],
            'images' => [
                'title' => 'Images',
                'settings' => [
                    'icon' => 'image',
                    'icon_color' => 'blue darken-2',
                ],
            ],
            'videos' => [
                'title' => 'Videos',
                'settings' => [
                    'icon' => 'video_library',
                    'icon_color' => 'red darken-2',
                ],
            ],
            'employees' => [
                'title' => 'Employees',
                'settings' => [
                    'icon' => 'groups',
                    'icon_color' => 'light-blue darken-2',
                ],
            ],
            'account_settings' => [
                'title' => 'Account Settings',
                'settings' => [
                    'icon' => 'manage_accounts',
                    'icon_color' => 'orange darken-2',
                ],
            ],
        ],

        'settings' => [
            'groups' => [
                'fallback' => 'Backlog',

                'defaults' => [
                    ['name' => 'Backlog', 'color' => null],
                    ['name' => 'In Progress', 'color' => null],
                    ['name' => 'Done', 'color' => null],
                ],
            ],

            'labels' => [
                'defaults' => [
                    ['name' => '', 'color' => '#E91E63'],
                    ['name' => '', 'color' => '#9C27B0'],
                    ['name' => '', 'color' => '#009688'],
                    ['name' => '', 'color' => '#FF9800'],
                ],
                'available_colors' => [
                    '#E91E63',
                    '#9C27B0',
                    '#009688',
                    '#FF9800',
                    '#2196F3',
                    '#03A9F4',
                    '#1B5E20',
                    '#558B2F',
                    '#FFD600',
                    '#795548',
                    '#9E9E9E',
                    '#311B92',
                ],
            ],
        ],
    ],
];

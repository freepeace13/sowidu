<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Todo Service Language Lines
    |--------------------------------------------------------------------------
    */

    'labels' => [
        'add-to-board' => 'Add to board',
        'duplicate-board' => 'Duplicate board',
        'confirm-duplicate' => 'Do you want to duplicate this board?',
        'duplicate-success' => 'Success!',
        'delete-board' => 'Delete board',
        'confirm-delete' => 'Do you want to delete this board?',
        'delete' => 'Delete',
        'board-deleted' => 'Board has been deleted.',
        'delete-group' => 'Delete group',
        'confirm-delete-group' => 'Do you want to delete this group?',
    ],

    'hints' => [
        'search-cards-members' => 'Search cards, members, labels, and more.',
        'search-user' => 'Search User',
    ],

    'notifications' => [
        'comment' => [
            'created' => '<b>:author</b> commented on <b><u>:task</u></b>',
        ],

        'task' => [
            'updated' => [
                'group' => '<b>:causer</b> moved <b><u>:task</u></b> from <del>:old</del> to <ins>:new</ins>.',
            ],
            'member' => [
                'added' => '<b>:causer</b> added you on <b><u>:task</u></b>.',
            ],
        ],

        'subscriber' => [
            'added' => 'You are now subscribed to <a href=":link"><b><u>:board</u></b></a>',
            'removed' => 'You have been removed on board <b>:board</b>.',
        ],
    ],

    'activity' => [
        'board' => [
            'created' => '<b>:causer</b> created this board.',
            'updated' => [
                'title' => '<b>:causer</b> changed the board title.',
                'description' => '<b>:causer</b> changed the board description.',
                'logo_path' => '<b>:causer</b> changed the board logo.',
            ],

            'subscriber' => [
                'added' => '<b>:causer</b> added <em>:user</em> to this board.',
                'removed' => '<b>:causer</b> removed <del>:user</del> to this board.',
            ],
        ],
        'task' => [
            'created' => '<b>:causer</b> added <a href=":link">:task</a> to <u>:group</u>.',
            'updated' => [
                'title' => '<b>:causer</b> changed the task title <del>:from</del> to <a href=":link"><ins>:task</ins></a>.',
                'group' => '<b>:causer</b> moved <a href=":link">:task</a> from <del>:from</del> to <ins>:to</ins>.',
                'description' => '<b>:causer</b> updated the task <a href=":link">:task</a> description.',
            ],
            'deleted' => '<b>:causer</b> deleted task <del>:task</del> from <u>:group</u>.',

            'member' => [
                'added' => '<b>:causer</b> added <em>:user</em> to <a href=":link">:task</a>.',
                'added_auth_user' => '<b>:causer</b> joined on <a href=":link">:task</a>.',
                'removed' => '<b>:causer</b> removed <del>:user</del> to <a href=":link">:task</a>.',
                'removed_auth_user' => '<b>:causer</b> left <a href=":link">:task</a>.',
            ],

            'comment' => [
                'created' => '<b>:causer</b> on <a href=":link">:task</a>.',
            ],
        ],

        /**
         * This will be displayed on the Task Viewer or task specific modal
         */
        'task_viewer' => [
            'created' => '<b>:causer</b> created this card.',
            'updated' => [
                'title' => '<b>:causer</b> changed the title.',
                'group' => '<b>:causer</b> moved this task from <del>:from</del> to <ins>:to</ins>.',
                'description' => '<b>:causer</b> updated this task description.',
            ],
            'member' => [
                'added' => '<b>:causer</b> added <em>:user</em> to this card.',
                'added_auth_user' => '<b>:causer</b> joined this card.',
                'removed' => '<b>:causer</b> removed <del>:user</del> to this card.',
                'removed_auth_user' => '<b>:causer</b> left on this card.',
            ],
        ],
    ],
];

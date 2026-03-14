<?php

return [
    'types' => [
        'incomplete_address' => [
            'enabled' => true,
            'priority' => 2,
            'expiration' => 'time',
            'resolver' => \App\Extensions\Popup\Resolvers\IncompleteAddress::class,
            'client_form' => 'IncompleteAddress',
        ],

        'company_confirmation' => [
            'enabled' => true,
            'priority' => 1,
            'expiration' => 'time',
            'resolver' => \App\Extensions\Popup\Resolvers\CompanyConfirmation::class,
            'client_form' => 'CompanyConfirmation',
        ],
    ],
];

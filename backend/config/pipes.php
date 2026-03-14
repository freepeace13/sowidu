<?php

return [
    'deliveryTicket' => [
        'import' => [
            App\Actions\DeliveryTicket\Import\ImportProcessor::class,
            // App\Actions\DeliveryTicket\Import\CatalogProcessor::class,
        ],
    ],
];

<?php

return [
    'labels' => [
        'filter-by-event' => 'Filter by event',
        'filter-by-employees' => 'Filter by employees',
        'filter-by-date-range' => 'Filter by date range',
        'filter-by-order' => 'Filter by Order #',
        'filter-by-address' => 'Filter by Address',
        'filter-by-invoice-status' => 'Filter by Invoice Status',
        'started' => 'Started',
        'finished' => 'Finished',
        'currently-working' => 'Currently Working',
        'show-reports' => 'Show Reports',
        'show-note' => 'Show Note',
        'create-manual-entry' => 'Create Manual Entry',
        'update-manual-entry' => 'Update Manual Entry',
        'status' => 'Status',
        'form' => [
            'start-date' => 'Start Date',
            'start-time' => 'Start Time',
            'end-date' => 'End Date',
            'end-time' => 'End Time',
            'notes' => 'Notes',
            'event' => 'Event',
            'employee' => 'Select Employee',
            'payment_form' => 'Select Payment Form',
            'payment_payment' => 'Payment Method',
            'document_number' => 'Document Number',
            'document_date' => 'Document Date',
        ],
        'delivery-address' => 'Delivery Address',
        'started-at' => 'Started at',
        'ended-at' => 'Ended at',
        'duration' => 'Duration',
        'invoiced' => 'Invoiced',
        'open' => 'Open',
        'invoiced-not-paid' => 'Invoiced, not paid',
        'invoiced-paid' => 'Invoiced, paid',
        'total_hours' => ':hours hours and :minutes minutes',
        'payment_form' => 'Payment Form',
        'cancel-button' => 'Cancel',
        'save-button' => 'Save',
    ],

    'hints' => [
        'search-placeholder' => 'Search by Order #, Address, Employee, etc.',
    ],

    'notifications' => [
        'manual-entry' => [
            'error' => [
                'date_time_end' => 'End date/time must be greater than start date/time.',
            ],
            'created' => 'Created manual work log successfully.',
            'updated' => 'Updated work log successfully.',
            'deleted' => 'Deleted work log successfully.',
            'payment-form' => 'Payment form updated successfully.',
        ],
    ],

    'messages' => [
        'confirm-delete' => 'Do you want to delete this work log?',
        'no-reports' => 'Employee doesn\'t submitted any reports.',
    ],

    'payment_type' => [
        'paid_via_payroll' => 'Paid via Payroll',
        'paid_via_incoming_invoice' => 'Paid via Incoming Invoice',
    ],
];

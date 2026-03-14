<?php

return [
    'titles' => [
        'overview' => 'Order Overview',
    ],
    'labels' => [
        'order' => 'Order | Orders',
        'incoming-orders' => 'Incoming Orders',
        'incoming' => 'Incoming',
        'outgoing-orders' => 'Outgoing Orders',
        'outgoing' => 'Outgoing',
        'order-types' => 'Order Type',
        'orderbook' => 'Orderbook',
        'order-details' => 'Order details',
        'address-information' => 'Address information',
        'client-information' => 'Client information',
        'client-details' => 'Client details',
        'phone' => 'Phone',
        'delivery-address' => 'Delivery address',
        'delivery-date' => 'Delivery Date',
        'save-address' => 'Save Address',
        'status' => 'Status',
        'client' => 'Client',
        'contractor' => 'Contractor',
        'contractor-details' => 'Contractor details',
        'select' => 'Select',
        'add-new-address' => 'Add new address',
        'update-order' => 'Update Order',
        'order-document' => 'Order Documents',
        'media' => 'Medias',
        'incoming-invoices' => 'Incoming Invoices',
        'outgoing-invoices' => 'Outgoing Invoices',
        'order-media' => 'Order Medias',
        'order-files' => 'Order Files',
        'order-documents' => 'Order Documents',
        'add-document' => 'Add Document',
        'add-incoming-invoice' => 'Add Incoming Invoice',
        'add-outgoing-invoice' => 'Add Outgoing Invoice',
        'confirm-sharing-to-opposite-party' => 'Are you sure you would like to share this file with the other party on this order?',
        'attach-media' => 'Attach Media',
        'order-in-progress' => 'Employee is still working on this order...',
        'search-placeholder' => 'Search for Contractor / Billing Address / Description',
        'used-products' => 'Used Products',

        // New
        'show-all' => 'Show All',
        'show-incoming' => 'Show Incoming',
        'show-outgoing' => 'Show Outgoing',
        'filter-status' => 'Filter Status',
        'filter-invoice' => 'Invoice',
        'date_added' => 'Date added',
        'order_status' => 'Order Status',
        'from' => 'From',
        'to' => 'To',
        'order-invoices' => 'Order Invoices',

        'inputs' => [
            'order-date' => 'Order date',
            'planned-start-date' => 'Planned Start Date',
            'planned-finish-date' => 'Planned Finish Date',
            'description' => 'Order description',
        ],
        'hide-details' => 'Hide details',
        'currently-working' => 'Currently Working',
        'create-report' => 'Create Report',
        'edit-report' => 'Edit Report',
        'add-report' => 'Add Report',
        'report' => 'Report',
        'reports' => 'Reports',
        'share-report' => 'Share to client',
        'update-client' => 'Update client',
        'upload-order-media' => 'Attach Files  to Order',
        'tag-category' => 'Tag Category',
        'tag-address' => 'Tag Address',
        'add-files' => 'Add Files',
        'add-more' => 'Add More',
        'empty-orders' => 'There are no orders.',
        'change-order' => 'Change Order',
        'edit-delivery-address' => 'Edit Order Delivery Address',
        'edit-planned-start-date' => 'Edit Planned Start Date',
        'edit-planned-finish-date' => 'Edit Planned Finish Date',
        'edit-description' => 'Edit Description',
        'order-number' => 'Order Number',
        'order-offers' => 'Order Offers',
    ],
    'messages' => [
        'updated' => 'Order has been updated.',
    ],

    'hints' => [
        'search-contractor' => 'Search contractor name...',
        'add-input-as-new-contact' => 'Add :input as new contact',
        'past-date' => 'The date is in the past, are you sure?',
    ],

    'incoming' => [
        'buttons' => [
            'create' => 'Create incoming',
        ],
        'labels' => [
            'create-order' => 'Create New Incoming Order',
            'update-order' => 'Update Incoming Order',
        ],
    ],
    'outgoing' => [
        'buttons' => [
            'create' => 'Create outgoing',
        ],
        'labels' => [
            'create-order' => 'Create New Outgoing Order',
            'update-order' => 'Update Outgoing Order',
        ],
    ],
    'notifications' => [
        'incoming' => [
            'client-new-outgoing-order' => 'You have an outgoing order from <b>:contractor</b>.',
        ],
        'outgoing' => [
            'contractor-new-incoming-order' => 'You have an incoming order from <b>:client</b>.',
            'created' => 'New outgoing order has been created.',
        ],
        'order' => [
            'updated' => '<b>:causer</b> has updated the order <i>:changes</i>.',
            'commissioned' => '<b>:causer</b> has confirmed the order.',
            'cancelled' => '<b>:causer</b> has cancelled the order.',
            'started' => '<b>:causer</b> has started working on your order.',
            'ready-for-review' => '<b>:causer</b> is done on the order, please review contractor works.',
            'reject' => '<b>:causer</b> has rejected the order approval!',
            'finished' => '<b>:causer</b> has mark this order finished. Good job!',
            'work-on-revisions' => '<b>:causer</b> has started working on your revisions.',
        ],
        'employee-still-working' => 'There are still employees working, so we cannot move this order to the review stage.',
        'order-media' => [
            'attached' => 'Media has been attached to this order.',
            'detached' => 'Media has been detached to this order.',
            'warning-no-category' => 'You did not specify a category, do you want to proceed attaching media?',
        ],
        'product' => [
            'added' => 'Products has been added to order.',
            'updated' => 'Product quantity has been updated.',
            'removed' => 'The product is no longer included in the order.',
        ],
        'delivery-ticket-materials' => [
            'added' => 'Delivery ticket materials has been added to order.',
        ],
    ],
    'status' => [
        'response' => [
            'message' => [
                'commissioned' => 'Confirm this order?',
                'waiting-for-client-confirmation' => 'Waiting for client confirmation.',
                'started' => 'Would you like to start the order?',
                'waiting-for-contractor-to-start' => 'Waiting for contractor to start.',
                'ongoing' => 'Contractor is still working on your order.',
                'ready-for-review' => 'Done? Notify client and let them review?',
                'waiting-for-client-review' => 'Client is still reviewing the order...',
                'finished' => 'Order is fulfilled?',
                'fulfilled' => 'Order is fulfilled.',
                'waiting-for-contractor-confirmation' => 'Waiting for contractor confirmation.',
                'work-on-revisions' => 'Client wants some revisions.',
                'waiting-for-contractor-revision' => 'The contractor is still working on your revisions.',
                'cancelled' => 'This order was cancelled.',
                'contractor-waiting-for-client-confirmation' => 'Waiting for client confirmation.',
                'warning-before-force-order-confirmation' => 'Attention, normally the client should confirm that order. Are you sure to change status without client confirmation?',
            ],
        ],
        'title' => [
            'CANCELLED' => 'Cancelled',
            'IN_PREPARATION' => 'In Preparation',
            'WAITING_FOR_CLIENT_CONFIRMATION' => 'Waiting',
            'CONTRACTOR_WAITING_FOR_CLIENT_CONFIRMATION' => 'Waiting',
            'WAITING_FOR_CONTRACTOR_CONFIRMATION' => 'Confirming',
            'COMMISSIONED' => 'Commissioned',
            'WAITING_FOR_CONTRACTOR_TO_START' => 'Waiting',
            'STARTED' => 'Started',
            'ONGOING' => 'Ongoing',
            'READY_FOR_REVIEW' => 'Ready for review',
            'WAITING_FOR_CLIENT_REVIEW' => 'Client Reviewing', // Awaiting client feedback
            'REJECT' => 'Reject',
            'WORK_ON_REVISIONS' => 'Ongoing', // Making adjustments
            'WAITING_FOR_CONTRACTOR_REVISION' => 'Client Reviewing',
            'FINISHED' => 'Finished',
            'FULFILLED' => 'Done All',
        ],
        'description' => [
            'CANCELLED' => 'This order was cancelled.',
            'IN_PREPARATION' => 'Order is under preparation.',
            'WAITING_FOR_CLIENT_CONFIRMATION' => 'Waiting for client confirmation.',
            'CONTRACTOR_WAITING_FOR_CLIENT_CONFIRMATION' => 'Waiting for client confirmation.',
            'WAITING_FOR_CONTRACTOR_CONFIRMATION' => 'Waiting for contractor confirmation.',
            'COMMISSIONED' => 'Order has been approved.',
            'WAITING_FOR_CONTRACTOR_TO_START' => 'Waiting for contractor to start.',
            'STARTED' => 'Order is ongoing.',
            'ONGOING' => 'Order is still in progress.',
            'READY_FOR_REVIEW' => 'Ready for review.',
            'WAITING_FOR_CLIENT_REVIEW' => 'Awaiting client feedback.', // Awaiting client feedback
            'REJECT' => 'Client has rejected the order.',
            'WORK_ON_REVISIONS' => 'Processing changes.', // Making adjustments
            'WAITING_FOR_CONTRACTOR_REVISION' => 'Awaiting contractor modification.',
            'FINISHED' => 'Order is completed. Work is done.',
            'FULFILLED' => 'Order is completed. Work is done.',
        ],
    ],
    'buttons' => [
        'start' => 'Start Working',
        'stop-working' => 'Stop Working',
        'ready-for-review' => 'For Review',
        'finished' => 'Finished',
        'reject' => 'Reject',
        'work-on-revisions' => 'Work on Revisions',
        'force-confirm' => 'Confirm for Client',
        'ful-fill' => 'Fulfill Order',
        'show-timeline' => 'Show timeline',
        'resume-working' => 'Resume Working',
        'show-more-details' => 'Show more details',
        'time-logs' => 'Time logs',
        'report' => 'Report',
        'create-order' => 'Create Order',
        'filter' => 'Filter',
        'add-offer' => 'Add Offer',
    ],

    'timelines' => [
        'created' => 'Order created by <i>:causer</i>.',
        'cancelled' => 'Order cancelled by <i>:causer</i>.',
        'confirmed' => 'Order confirmed by <i>:causer</i>.',
        'start_working' => '<i>:causer</i> has started working.',
        'finish_working' => '<i>:causer</i> has finished working.',
        'client_rejected' => 'Order was rejected by <i>:causer</i>.',
        'client_accepted' => 'Order was accepted by <i>:causer</i>.',
        'completed' => 'Order completed.',
    ],

    'errors' => [
        'employee-cannot-work-on-order' => 'This order cannot be started because you are still working on another order. Please stop the time on other order first. <a href=":order-link" target="_blank" class="tw-text-info tw-text-underline tw-font-bold">Go to Order</a>',
        'employee-cannot-create-report' => 'You cannot add a report if you are not working on this order.',
        'empty-reports' => 'There are no reports submitted on this Order.',
        'order-not-found' => 'Order not found!',
        'order-not-owned' => 'Order is not owned by your company.',
    ],

    'products' => [
        'order-products' => 'Order Used Products',
        'add-product' => 'Add Product',
        'add-product-to-this-order' => 'Add Product to this Order',
        'confirm-delete-product' => 'Are you sure you want to remove this product from this order?',
        'quantity' => 'Quantity',

        'form' => [
            'filter-by-type' => 'Filter by type',
        ],
    ],

    'invoices' => [
        'back-to-invoices' => 'Back to Invoices',
        'add-invoice' => 'Add Invoice',

        'messages' => [
            'no-invoices' => 'There are no invoices on this order.',
        ],
    ],

    'delivery_tickets' => [
        'order-delivery-tickets' => 'Order Delivery Tickets',
        'add-delivery' => 'Add Delivery',
        'message' => [
            'delivery-ticket-is-paid' => 'This delivery ticket is already paid.',
        ],
    ],

    'work_log' => [
        'order-work-logs' => 'Work Logs',
        'changed_order' => 'Work log order has been changed.',
        'create-manual-entry' => 'Create Manual Entry',
        'manual-entry-created' => 'Manual entry has been created.',

        'form' => [
            'search-order' => 'Search order number, delivery address or client name...',
        ],
    ],
];

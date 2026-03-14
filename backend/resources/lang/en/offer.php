<?php

return [
    'labels' => [
        'offer' => 'Offer',
        'incoming_offer' => 'Incoming Offer',
        'outgoing_offer' => 'Outgoing Offer',
        'amount' => 'Amount',
        'create_offer' => 'Create Offer',
        'edit_offer' => 'Edit Offer',
        'status' => [
            'draft' => 'Draft',
            'pending' => 'Pending',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ],
        'offer_details' => 'Offer Details',
        'offer_number' => 'Offer Number',
        'items' => 'Items',
        'attach_items' => 'Attach Items',
        'notes' => 'Notes',
        'subject' => 'Subject',
        'message' => 'Message',
        'edit-subject-message-notes' => 'Edit subject, message and notes',
    ],

    'buttons' => [
        'create' => 'Create Offer',
        'edit' => 'Edit Offer',
        'delete' => 'Delete Offer',
        'view' => 'View Offer',
        'search' => 'Search Offers',
        'attach_to_offer' => 'Attach to Offer',
        'send_offer' => 'Send Offer',
        'attach_item' => 'Attach Item',
        'reject' => 'Reject',
        'accept' => 'Accept',
        'preview' => 'Preview',
        'edit-notes' => 'Edit Notes',
        'download_pdf' => 'Download PDF',
        'edit-subject-description' => 'Edit Subject, Description and Notes',
        'edit-offer-details' => 'Edit Offer Details',
        'view-order' => 'View Order',
        'attach-manual-item' => 'Attach Manual Item',
    ],

    'inputs' => [
        'title' => 'Title',
        'type' => 'Type',
        'recipient' => 'Recipient',
        'offer_date' => 'Offer Date',
        'description' => 'Description',
        'construction_site' => 'Construction Site',
        'execution_period_start' => 'Execution Period Start',
        'execution_period_end' => 'Execution Period End',
        'execution_period' => 'Execution Period',
        'order' => 'Order',
    ],

    'hints' => [
        'search-offer' => 'Search for offers by title, recipient, or offer number...',
        'search-recipient' => 'Search for recipients by name or email...',
        'search-order' => 'Search for orders by order number, client, or contractor...',
        'link-order-placeholder' => 'Link an order to this offer.',
    ],

    'messages' => [
        'created' => 'Offer created successfully.',
        'updated' => 'Offer updated successfully.',
        'deleted' => 'Offer deleted successfully.',
        'deleting' => 'Are you sure you want to delete this offer? This action cannot be undone.',
        'subject-message-notes-updated' => 'Subject, message, and notes updated successfully.',

        'current_status' => [
            'draft' => 'This offer is currently in draft status.',
            'sent' => 'This offer is sent, to proceed you need to accept or reject it.',
            'rejected' => 'This offer has been rejected by the recipient.',
            'cancelled' => 'This offer has been cancelled.',
            'accepted' => 'This offer has been accepted by the recipient. Order is already created.',
        ],

        'statuses' => [
            'sending' => 'Are you sure you want to send this offer to the recipient?',
            'accepting' => 'Are you sure you want to accept this offer?',
            'rejecting' => 'Are you sure you want to reject this offer?',
            'cancelling' => 'Are you sure you want to cancel this offer?',

            'sent' => 'Offer sent successfully.',
            'accepted' => 'The offer has been accepted.',
            'rejected' => 'The offer has been rejected.',
            'completed' => 'The offer has been completed.',
            'cancelled' => 'The offer has been cancelled.',
        ],

        'items' => [
            'empty' => 'No items attached to this offer.',
            'added' => 'Item added to the offer successfully.',
            'removed' => 'Item removed from the offer successfully.',
            'updated' => 'Item updated successfully.',
            'removing' => 'Are you sure you want to remove this item from the offer? This action cannot be undone.',
            'manual_item_added' => 'Manual item added to the offer successfully.',

        ],

        'copied-to-clipboard' => 'URL copied to clipboard!',

        'failed' => [
            'empty-offer' => 'Offer cannot be sent because it has no items.',
            'not-found' => 'Offer not found.',
        ],

        'no-offers' => 'No offers found.',

        'configuration' => [
            'updated' => 'Offer configuration updated successfully.',
        ],
    ],

    'items' => [
        'labels' => [
            'edit_item' => 'Edit Item',
            'add_manual_item' => 'Add Manual Item',
            'update_manual_item' => 'Update Manual Item',
        ],

        'inputs' => [
            'quantity' => 'Quantity',
            'price' => 'Price',
            'total' => 'Total',
        ],
    ],

    'notifications' => [
        'new_offer' => 'You have a new offer from :sender. Please check it out.',
        'offer_accepted' => 'Your offer has been accepted by :causer. Please check it out.',
        'offer_rejected' => 'Your offer has been rejected by :causer. Please check it out.',
    ],
];

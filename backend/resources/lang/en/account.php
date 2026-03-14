<?php

return [
    'labels' => [
        'profile' => 'Profile',
        'organizations-profile' => 'Organizations Profile',
        'organizations' => 'Organizations',
        'employees' => 'Employees',
        'categories' => 'Categories',
        'category' => 'Category',
        'category-settings' => 'Category Settings',
        'media-settings' => 'Media Settings',
        'create-category' => 'Create Category',
        'organization-founder' => 'Organization Founder',
        'settings-applied-to' => 'All settings below are automatically applied to the',
        'auto-share-to-selected' => 'Auto Share to selected',
        'media-will-be-auto-shared-to-roles' => 'Media files will be auto-shared to the selected roles.',
        'select-roles' => 'Select roles',
        'tax-settings' => 'Tax Settings',
        'invoice-settings' => 'Invoice Settings',
        'invoice-defaults' => 'Invoice Defaults',
        'offer-configuration' => 'Offer Configuration',
    ],
    'buttons' => [
        'manage-access' => 'Manage Access',
        'edit' => 'Edit',
        'inviting' => 'Inviting...',
    ],
    'inputs' => [
        'role-name' => 'Enter category name',
        'role-name-placeholder' => 'Enter role name',
        'bank-name' => 'Bank Name',
        'iban' => 'IBAN',
        'bic' => 'BIC',
        'managing-director' => 'Managing Director',
        'website' => 'Website',
        'company-email' => 'Company E-Mail Address',
        'commercial-register-hra' => 'HRA',
        'commercial-register-hrb' => 'HRB',
        'commercial-register' => 'Select Commercial Register',
        'commercial-register-number' => 'Commercial Register Number',
        'payment-terms' => 'Payment Terms',
    ],

    'categories' => [
        'labels' => [
            'role-is-enabled-on-media-settings' => 'This role is enabled on media settings.',
            'roles-where-media-shared' => 'Choose the roles where the media file in this category will be shared.',
        ],
    ],

    'messages' => [
        'role' => [
            'created' => 'New role has been created.',
            'updated' => 'Role has been updated.',
        ],
        'employee-role' => [
            'updated' => 'Employee role has been updated.',
        ],
        'category' => [
            'created' => 'New category has been created.',
            'updated' => 'Category has been updated.',
            'deleted' => 'Category has been deleted.',
            'confirm_deleting' => 'Do you want to delete this category?',
        ],
        'organization' => [
            'updated' => 'Organization profile has been updated.',
        ],

        'invoice-settings' => [
            'updated' => 'Invoice settings have been updated.',
        ],
    ],

    'employees' => [
        'labels' => [
            'rate' => 'Rate',
            'manage-rates' => 'Manage Rates',
        ],
        'buttons' => [
            'manage-rates' => 'Manage Rates',
        ],

        'messages' => [
            'rates' => [
                'updated' => 'Employee rates have been updated.',
            ],
        ],
    ],

    'tax' => [
        'labels' => [
            'taxes' => 'Taxes',
            'tax-name' => 'Tax Name',
            'tax-rate' => 'Tax Rate',
            'vat-identification-number' => 'VAT Identification Number',
            'company-tax-number' => 'Company Tax Number',
            'tax-default' => 'Default',
            'tax-actions' => 'Actions',
            'tax-create' => 'Create Tax',
            'tax-delete' => 'Are you sure you want to delete this tax?',
            'tax-is-default' => 'Mark as default, this will automatically include this tax on all invoices.',
            'default' => 'Default',
        ],

        'hints' => [
            'tax-default' => 'Mark an item as default to automatically include it on all invoices.',
        ],

        'messages' => [
            'created' => 'Tax created successfully.',
            'updated' => 'Tax updated successfully.',
            'deleted' => 'Tax have been deleted.',
            'vat-identification-number-updated' => 'VAT Identification Number has been updated.',
            'tax-number-updated' => 'Tax Number has been updated.',
        ],
    ],

    'manage-access' => [
        'labels' => [
            'company-settings' => 'Company Settings',
        ],
    ],

    'invoice-settings' => [
        'labels' => [
            'payment-terms-instructions' => 'Payment Terms Instructions',
        ],
    ],

    'offer-configuration' => [
        'labels' => [
            'default-values' => 'Default Values',
            'terms-and-conditions' => 'Terms and Conditions',
        ],
    ],
];

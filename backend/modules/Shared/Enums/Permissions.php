<?php

namespace Modules\Shared\Enums;

use App\Models\User;
use App\Services\AppServices;
use ReflectionClass;

class Permissions
{
    /**
     * Company Settings
     */
    const CAN_CHANGE_AVATAR = 'change avatar';

    const CAN_UPDATE_SETTINGS = 'update settings';
    const CAN_MANAGE_ORGANIZATION_SETTINGS = 'can manage organization settings';
    const CAN_MANAGE_ORGANIZATION_CATEGORIES = 'can manage organization categories';
    const CAN_MANAGE_EMPLOYEE_RATES = 'can manage employee rates';
    const CAN_MANAGE_TAX = 'can manage tax';

    /**
     * Manage Accounts
     */
    const CAN_ACCESS_EMPLOYEES = 'access_employees';

    const CAN_MANAGE_PERMISSIONS = 'manage permissions';
    const CAN_ADD_MEMBER = 'add member';

    /**
     * Media
     */
    const CAN_ACCESS_MEDIA = 'can access media';

    /**
     * Chat
     */
    const CAN_ACCESS_CHAT = 'can access chat';

    /**
     * To-do
     */
    const CAN_ACCESS_TODO = 'can access todo';

    /**
     * Addressbook
     */
    const CAN_ACCESS_ADDRESS_BOOK = 'can access address book';

    const CAN_MANAGE_ADDRESS_BOOK = 'can manage address book';

    /**
     * Order
     */
    const CAN_ACCESS_ORDER = 'can access order';

    const CAN_UPDATE_ORDER = 'can update order';
    const CAN_CREATE_ORDER = 'can create order';
    const CAN_CONFIRM_ORDER = 'can confirm order';
    const CAN_CANCEL_ORDER = 'can cancel order';
    const CAN_CHANGE_WORK_LOGS_ORDER = 'can change work logs order';
    const CAN_SHARE_MEDIA = 'can share media';
    const CAN_MANAGE_ORDER_PRODUCTS = 'can manage order products';
    const CAN_MANAGE_ORDER_DELIVERY_TICKETS = 'can manage order delivery tickets';

    /**
     * Catalog
     */
    const CAN_ACCESS_CATALOG = 'can access catalog';

    const CAN_CREATE_CATALOG_ITEMS = 'can create catalog items';
    const CAN_DELETE_CATALOG_ITEMS = 'can delete catalog items';
    const CAN_VIEW_PURCHASING_PRICE = 'can view purchasing price';
    const CAN_VIEW_SELLING_PRICE = 'can view selling price';
    const CAN_CREATE_MANUAL_ENTRY_FOR_EMPLOYEES = 'can create manual entry for employees'; // tracked

    /**
     * Delivery Tickets
     */
    const CAN_ACCESS_DELIVERY_TICKETS = 'can access delivery tickets';

    const CAN_MANAGE_DELIVERY_TICKETS = 'can manage delivery tickets';
    const CAN_MANAGE_MATERIALS_TO_DELIVERY_TICKETS = 'can add materials to delivery tickets';

    /**
     * Invoices
     */
    const CAN_ACCESS_INVOICES = 'can access invoices';

    const CAN_MANAGE_INVOICES = 'can manage invoices';
    const CAN_MANAGE_INVOICES_DOCUMENTS = 'can manage invoices documents';
    const CAN_MANAGE_INVOICES_ITEMS = 'can manage invoices items';
    const CAN_SEND_INVOICES_TO_CLIENT = 'can send invoices to client';
    const CAN_MARK_INVOICES_AS_PAID = 'can mark invoices as paid';
    const CAN_MANAGE_TAXES = 'can manage taxes';
    const CAN_VIEW_INVOICE_PAYMENTS = 'can view invoice payments';
    const CAN_MANAGE_INVOICE_PAYMENTS = 'can manage invoice payments';
    const CAN_MANAGE_INVOICE_MANUAL_ITEMS = 'can manage invoice manual items';

    /**
     * Work Logs
     */
    const CAN_ACCESS_WORK_LOGS = 'can access work logs';

    const CAN_MANAGE_WORK_LOGS = 'can manage work logs';
    const CAN_ADD_MANUAL_WORK_LOG_ENTRY = 'can add manual work log entry';
    const CAN_VIEW_OTHERS_WORK_LOGS = 'can view others work logs'; // TODO
    const CAN_ADD_MANUAL_WORK_LOG_ENTRY_FOR_OTHERS = 'can add manual work log entry for others'; // TODO

    /**
     * Add your new permissions above here...
     */
    public static function all(): array
    {
        $self = new ReflectionClass(__CLASS__);

        return collect($self->getConstants())->values()
            ->toArray();
    }

    public static function groupings(): array
    {
        return [
            [
                'label' => __('account.manage-access.labels.company-settings'),
                'icon' => 'room_preferences',
                'permissions' => [
                    self::CAN_CHANGE_AVATAR,
                    self::CAN_UPDATE_SETTINGS,
                    self::CAN_MANAGE_ORGANIZATION_SETTINGS,
                    self::CAN_MANAGE_ORGANIZATION_CATEGORIES,
                    self::CAN_MANAGE_EMPLOYEE_RATES,
                    self::CAN_MANAGE_TAX,
                    self::CAN_CREATE_MANUAL_ENTRY_FOR_EMPLOYEES,
                ],
            ],
            [
                'label' => __('account.labels.employees'),
                'icon' => 'groups',
                'color' => 'link',
                'permissions' => [
                    self::CAN_ACCESS_EMPLOYEES,
                    self::CAN_MANAGE_PERMISSIONS,
                    self::CAN_ADD_MEMBER,
                ],
            ],
            [
                ...AppServices::findService('media', ['label', 'icon', 'color', 'name']),
                'permissions' => [
                    self::CAN_ACCESS_MEDIA,
                ],
            ],
            [
                ...AppServices::findService('chat', ['label', 'icon', 'color', 'name']),
                'permissions' => [
                    self::CAN_ACCESS_CHAT,
                ],
            ],
            [
                ...AppServices::findService('todo', ['label', 'icon', 'color', 'name']),
                'permissions' => [
                    self::CAN_ACCESS_TODO,
                ],
            ],
            [
                ...AppServices::findService('addressbook', [
                    'label',
                    'icon',
                    'color',
                    'name',
                ]),
                'permissions' => [
                    self::CAN_ACCESS_ADDRESS_BOOK,
                    self::CAN_MANAGE_ADDRESS_BOOK,
                ],
            ],
            [
                ...AppServices::findService('order', ['label', 'icon', 'color', 'name']),
                'permissions' => [
                    self::CAN_ACCESS_ORDER,
                    self::CAN_UPDATE_ORDER,
                    self::CAN_CREATE_ORDER,
                    self::CAN_CONFIRM_ORDER,
                    self::CAN_CANCEL_ORDER,
                    self::CAN_CHANGE_WORK_LOGS_ORDER,
                    self::CAN_SHARE_MEDIA,
                    self::CAN_MANAGE_ORDER_PRODUCTS,
                    self::CAN_MANAGE_ORDER_DELIVERY_TICKETS,
                ],
            ],
            [
                ...AppServices::findService('work_logs', ['label', 'icon', 'color', 'name']),
                'permissions' => [
                    self::CAN_ACCESS_WORK_LOGS,
                    self::CAN_MANAGE_WORK_LOGS,
                    self::CAN_ADD_MANUAL_WORK_LOG_ENTRY,
                    self::CAN_VIEW_OTHERS_WORK_LOGS,
                    self::CAN_ADD_MANUAL_WORK_LOG_ENTRY_FOR_OTHERS,
                ],
            ],
            [
                ...AppServices::findService('catalogue', [
                    'label',
                    'icon',
                    'color',
                    'name',
                ]),
                'permissions' => [
                    self::CAN_ACCESS_CATALOG,
                    self::CAN_CREATE_CATALOG_ITEMS,
                    self::CAN_DELETE_CATALOG_ITEMS,
                    self::CAN_VIEW_PURCHASING_PRICE,
                    self::CAN_VIEW_SELLING_PRICE,
                ],
            ],
            [
                ...AppServices::findService('delivery_tickets', [
                    'label',
                    'icon',
                    'color',
                    'name',
                ]),
                'permissions' => [
                    self::CAN_ACCESS_DELIVERY_TICKETS,
                    self::CAN_MANAGE_DELIVERY_TICKETS,
                    self::CAN_MANAGE_MATERIALS_TO_DELIVERY_TICKETS,
                ],
            ],
            [
                ...AppServices::findService('invoices', [
                    'label',
                    'icon',
                    'color',
                    'name',
                ]),
                'permissions' => [
                    self::CAN_ACCESS_INVOICES,
                    self::CAN_MANAGE_INVOICES,
                    self::CAN_MANAGE_INVOICES_DOCUMENTS,
                    self::CAN_MANAGE_INVOICES_ITEMS,
                    self::CAN_SEND_INVOICES_TO_CLIENT,
                    self::CAN_MARK_INVOICES_AS_PAID,
                    self::CAN_MANAGE_TAXES,
                    self::CAN_VIEW_INVOICE_PAYMENTS,
                    self::CAN_MANAGE_INVOICE_PAYMENTS,
                    self::CAN_MANAGE_INVOICE_MANUAL_ITEMS,
                ],
            ],
        ];
    }

    public static function isSuperAdmin(User $user): bool
    {
        return in_array($user->email, config('app.super_admins'));
    }

    public static function forImpersonatorOnly(): array
    {
        return [
            self::CAN_ACCESS_DELIVERY_TICKETS,
            self::CAN_ACCESS_WORK_LOGS,
            self::CAN_ACCESS_INVOICES,
        ];
    }
}

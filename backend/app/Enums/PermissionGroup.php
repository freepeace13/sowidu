<?php

namespace App\Enums;

enum PermissionGroup: string
{
    case TeamSettings = 'team_settings';
    case TeamMemberManagement = 'team_member_management';
    case Media = 'media';
    case Chat = 'chat';
    case Todo = 'todo';
    case Addressbook = 'addresssbook';
    case Order = 'order';
    case Cataglogue = 'catalogue';
    case DeliveryTickets = 'delivery_tickets';
    case Invoices = 'invoices';

    public static function all()
    {
        return collect(self::cases())->map(fn (self $group) => [
            'label' => $group->label(),
            'permissions' => $group->items(),
        ]);
    }

    public function label()
    {
        return match ($this) {
            self::TeamSettings => 'Team',
            self::TeamMemberManagement => 'Team Members',
            self::Media => 'Media',
            self::Chat => 'Chat',
            self::Todo => 'Todo',
            self::Addressbook => 'Addressbook',
            self::Order => 'Order',
            self::Cataglogue => 'Catalogue',
            self::DeliveryTickets => 'Delivery Tickets',
            self::Invoices => 'Invoices',
        };
    }

    public function items()
    {
        return match ($this) {
            self::TeamSettings => [
                Permissions::CAN_CHANGE_AVATAR,
                Permissions::CAN_UPDATE_SETTINGS,
                Permissions::CAN_MANAGE_ORGANIZATION_SETTINGS,
                Permissions::CAN_MANAGE_ORGANIZATION_CATEGORIES,
                Permissions::CAN_MANAGE_EMPLOYEE_RATES,
                Permissions::CAN_MANAGE_TAX,
            ],

            self::TeamMemberManagement => [
                Permissions::CAN_ACCESS_EMPLOYEES,
                Permissions::CAN_MANAGE_PERMISSIONS,
                Permissions::CAN_ADD_MEMBER,
            ],

            self::Media => [
                Permissions::CAN_ACCESS_MEDIA,
            ],

            self::Chat => [
                Permissions::CAN_ACCESS_CHAT,
            ],

            self::Todo => [
                Permissions::CAN_ACCESS_TODO,
            ],

            self::Addressbook => [
                Permissions::CAN_ACCESS_ADDRESS_BOOK,
                Permissions::CAN_MANAGE_ADDRESS_BOOK,
            ],

            self::Order => [
                Permissions::CAN_ACCESS_ORDER,
                Permissions::CAN_UPDATE_ORDER,
                Permissions::CAN_CREATE_ORDER,
                Permissions::CAN_CONFIRM_ORDER,
                Permissions::CAN_CANCEL_ORDER,
                Permissions::CAN_ACCESS_WORK_LOGS,
                Permissions::CAN_CHANGE_WORK_LOGS_ORDER,
                Permissions::CAN_SHARE_MEDIA,
                Permissions::CAN_MANAGE_ORDER_PRODUCTS,
                Permissions::CAN_MANAGE_ORDER_DELIVERY_TICKETS,
            ],

            self::Cataglogue => [
                Permissions::CAN_ACCESS_CATALOG,
                Permissions::CAN_CREATE_CATALOG_ITEMS,
                Permissions::CAN_DELETE_CATALOG_ITEMS,
                Permissions::CAN_VIEW_PURCHASING_PRICE,
                Permissions::CAN_VIEW_SELLING_PRICE,
                Permissions::CAN_CREATE_MANUAL_ENTRY_FOR_EMPLOYEES,
            ],

            self::DeliveryTickets => [
                Permissions::CAN_ACCESS_DELIVERY_TICKETS,
                Permissions::CAN_MANAGE_DELIVERY_TICKETS,
                Permissions::CAN_MANAGE_MATERIALS_TO_DELIVERY_TICKETS,
            ],

            self::Invoices => [
                Permissions::CAN_ACCESS_INVOICES,
                Permissions::CAN_MANAGE_INVOICES,
                Permissions::CAN_MANAGE_INVOICES_DOCUMENTS,
                Permissions::CAN_MANAGE_INVOICES_ITEMS,
                Permissions::CAN_SEND_INVOICES_TO_CLIENT,
                Permissions::CAN_MARK_INVOICES_AS_PAID,
                Permissions::CAN_MANAGE_TAXES,
            ],
        };
    }
}

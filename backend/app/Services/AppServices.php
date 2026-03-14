<?php

namespace App\Services;

use App\Enums\Permissions;
use Illuminate\Support\Arr;

class AppServices
{
    public static function all(): array
    {
        return (new static)->get();
    }

    public static function findService(string $name, $keys = null): array
    {
        return Arr::only(collect((new static)->get())->where('name', $name)
            ->first(), $keys);
    }

    public function get(): array
    {
        return [
            [
                'name' => 'media',
                'label' => trans('headings.media-library'),
                'route' => route('media.drive.index'),
                'icon' => 'perm_media',
                'color' => 'red darken-2',
                'permission' => Permissions::CAN_ACCESS_MEDIA,
                'onDenied' => 'disable',
            ],
            [
                'name' => 'order',
                'label' => trans('headings.orders'),
                'route' => route('orders.overview'),
                'icon' => 'shopping_cart',
                'color' => 'blue',
                'permission' => Permissions::CAN_ACCESS_ORDER,
                'onDenied' => 'disable',
            ],
            [
                'name' => 'invoices',
                'label' => trans('headings.invoices'),
                'route' => route('invoices.index'),
                'icon' => 'receipt_long',
                'color' => 'light-green',
                'permission' => Permissions::CAN_ACCESS_INVOICES,
                'onDenied' => 'hide',
            ],
            [
                'name' => 'offers',
                'label' => trans('headings.offers'),
                'route' => route('offers.index'),
                'icon' => 'redeem',
                'color' => 'deep-orange',
                'permission' => Permissions::CAN_ACCESS_OFFERS,
                'onDenied' => 'hide',
            ],
            [
                'name' => 'my-offers',
                'label' => trans('headings.my-offers'),
                'route' => route('my-offers.index'),
                'icon' => 'discount',
                'color' => 'pink',
                'permission' => Permissions::CAN_ACCESS_MY_OFFERS,
                'onDenied' => 'hide',
            ],
            [
                'name' => 'catalogue',
                'label' => trans('headings.catalogue'),
                'route' => route('catalog.index'),
                'icon' => 'menu_book',
                'color' => 'cyan',
                'permission' => Permissions::CAN_ACCESS_CATALOG,
                'onDenied' => 'hide',
            ],
            [
                'name' => 'delivery_tickets',
                'label' => trans('headings.delivery_tickets'),
                'route' => route('delivery_tickets.index'),
                'icon' => 'local_shipping',
                'color' => 'teal',
                'permission' => Permissions::CAN_ACCESS_DELIVERY_TICKETS,
                'onDenied' => 'hide',
            ],
            [
                'name' => 'addressbook',
                'label' => trans('labels.addressbook'),
                'route' => route('addressbooks.people.index'),
                'icon' => 'perm_contact_calendar',
                'color' => 'indigo darken-1',
                'permission' => Permissions::CAN_ACCESS_ADDRESS_BOOK,
                'onDenied' => 'disable',
            ],
            [
                'name' => 'chat',
                'label' => trans('headings.chat'),
                'route' => route('chatly.index'),
                'icon' => 'chat',
                'color' => 'purple darken-2',
                'permission' => Permissions::CAN_ACCESS_CHAT,
                'onDenied' => 'disable',
            ],
            [
                'name' => 'todo',
                'label' => trans('headings.todo'),
                'route' => route('todos.boards.index'),
                'icon' => 'assignment',
                'color' => 'yellow darken-2',
                'permission' => Permissions::CAN_ACCESS_TODO,
                'onDenied' => 'disable',
            ],
            [
                'name' => 'employees',
                'label' => trans('labels.employees'),
                'route' => route('account.employees.index'),
                'icon' => 'groups',
                'color' => 'light-blue darken-2',
                'permission' => Permissions::CAN_ACCESS_EMPLOYEES,
                'onDenied' => 'hide',
            ],
            [
                'name' => 'work_logs',
                'label' => trans('headings.work-time-logs'),
                'route' => route('work_logs.index'),
                'icon' => 'work_history',
                'color' => 'green darken-2',
                'permission' => Permissions::CAN_ACCESS_WORK_LOGS,
                'onDenied' => 'hide',
            ],
            [
                'name' => 'account_settings',
                'label' => trans('labels.account-settings'),
                'route' => route('account.profile.index'),
                'icon' => 'manage_accounts',
                'color' => 'orange darken-2',
                'permission' => Permissions::CAN_UPDATE_SETTINGS,
                'onDenied' => 'hide',
            ],
            [
                'name' => 'app_settings',
                'label' => trans('labels.app-settings'),
                'route' => route('app.settings.index'),
                'icon' => 'admin_panel_settings',
                'color' => 'light-blue',
                'permission' => Permissions::SUPER_ADMIN,
                'onDenied' => 'hide',
            ],
        ];
    }

    public static function currencies(): array
    {
        return collect(config('app.default.currencies'))
            ->map(fn ($symbol, $value) => [
                'text' => "{$value} ({$symbol})",
                'symbol' => $symbol,
                'value' => $value,
            ])
            ->values()
            ->toArray();
    }

    public static function currencySymbol(?string $currency): string
    {
        if (blank($currency)) {
            return '';
        }

        return config('app.default.currencies')[$currency] ?? '';
    }
}

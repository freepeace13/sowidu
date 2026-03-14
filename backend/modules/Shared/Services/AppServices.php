<?php

namespace Modules\Shared\Services;

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
                'permission' => 'can access media',
                'onDenied' => 'disable',
            ],
            [
                'name' => 'order',
                'label' => trans('headings.orders'),
                'route' => route('orders.overview'),
                'icon' => 'shopping_cart',
                'color' => 'blue',
                'permission' => 'can access order',
                'onDenied' => 'disable',
            ],
            [
                'name' => 'invoices',
                'label' => trans('headings.invoices'),
                'route' => route('invoices.index'),
                'icon' => 'receipt_long',
                'color' => 'light-green',
                'permission' => 'can access invoices',
                'onDenied' => 'hide',
            ],
            [
                'name' => 'catalogue',
                'label' => trans('headings.catalogue'),
                'route' => route('catalogs.items.index'),
                'icon' => 'menu_book',
                'color' => 'cyan',
                'permission' => 'can access catalog',
                'onDenied' => 'hide',
            ],
            [
                'name' => 'delivery_tickets',
                'label' => trans('headings.delivery_tickets'),
                'route' => route('delivery_tickets.index'),
                'icon' => 'local_shipping',
                'color' => 'teal',
                'permission' => 'can access delivery tickets',
                'onDenied' => 'hide',
            ],
            [
                'name' => 'addressbook',
                'label' => trans('labels.addressbook'),
                'route' => route('addressbooks.people.index'),
                'icon' => 'perm_contact_calendar',
                'color' => 'indigo darken-1',
                'permission' => 'can access address book',
                'onDenied' => 'disable',
            ],
            [
                'name' => 'chat',
                'label' => trans('headings.chat'),
                'route' => route('chat.index'),
                'icon' => 'chat',
                'color' => 'purple darken-2',
                'permission' => 'can access chat',
                'onDenied' => 'disable',
            ],
            [
                'name' => 'todo',
                'label' => trans('headings.todo'),
                'route' => route('todos.boards.index'),
                'icon' => 'assignment',
                'color' => 'yellow darken-2',
                'permission' => 'can access todo',
                'onDenied' => 'disable',
            ],
            [
                'name' => 'employees',
                'label' => trans('labels.employees'),
                'route' => route('account.employees.index'),
                'icon' => 'groups',
                'color' => 'light-blue darken-2',
                'permission' => 'access_employees',
                'onDenied' => 'hide',
            ],
            [
                'name' => 'work_logs',
                'label' => trans('headings.work-time-logs'),
                'route' => route('work_logs.index'),
                'icon' => 'work_history',
                'color' => 'green darken-2',
                'permission' => 'can access work logs',
                'onDenied' => 'hide',
            ],
            [
                'name' => 'account_settings',
                'label' => trans('labels.account-settings'),
                'route' => route('account.profile.index'),
                'icon' => 'manage_accounts',
                'color' => 'orange darken-2',
                'permission' => 'update settings',
                'onDenied' => 'hide',
            ],
            [
                'name' => 'app_settings',
                'label' => trans('labels.app-settings'),
                'route' => route('app.settings.index'),
                'icon' => 'admin_panel_settings',
                'color' => 'light-blue',
                'permission' => 'is_super_admin',
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

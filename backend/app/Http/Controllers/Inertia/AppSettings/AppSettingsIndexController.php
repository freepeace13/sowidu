<?php

namespace App\Http\Controllers\Inertia\AppSettings;

use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppSettingsIndexController extends InertiaController
{
    public function __invoke(Request $request)
    {
        return Inertia::render('AppSettings/Index', [
            'settings' => [
                [
                    'name' => 'catalogue',
                    'label' => 'headings.catalogue',
                    'route' => route('app.settings.catalogs.units'),
                    'icon' => 'menu_book',
                    'color' => 'cyan',
                    'permission' => true,
                    'onDenied' => 'hide',
                ],
                [
                    'name' => 'translation-manager',
                    'label' => 'app_settings.labels.translation-manager',
                    'route' => route('app.settings.translation-manager'),
                    'icon' => 'translate',
                    'color' => 'green',
                    'permission' => true,
                    'onDenied' => 'hide',
                ],
                [
                    'name' => 'users-manager',
                    'label' => 'app_settings.labels.users-manager',
                    'route' => route('app.settings.manager.users'),
                    'icon' => 'groups',
                    'color' => 'dark-blue',
                    'permission' => true,
                    'onDenied' => 'hide',
                ],
                [
                    'name' => 'address-manager',
                    'label' => 'app_settings.labels.address-manager',
                    'route' => route('app.settings.addresses.manage'),
                    'icon' => 'maps_home_work',
                    'color' => 'purple',
                    'permission' => true,
                    'onDenied' => 'hide',
                ],
            ],
        ]);
    }
}

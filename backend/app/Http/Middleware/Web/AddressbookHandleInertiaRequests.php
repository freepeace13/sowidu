<?php

namespace App\Http\Middleware\Web;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;
use Sowidu\SharedData\SharedData;

class AddressbookHandleInertiaRequests extends HandleInertiaRequests
{
    public array $extraTranslations = ['addressbook'];

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'title' => 'Address Book',
            'defaults' => [
                'avatars' => [
                    'foreigner' => app(SharedData::class)->get('defaults.avatars.unset'),
                    'foreign_organization' => app(SharedData::class)->get('defaults.avatars.company'),
                ],
            ],
            'permissions' => [
                'manage_address' => filled($request->company()),
            ],
        ]);
    }
}

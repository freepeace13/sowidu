<?php

namespace App\Modules\Invoice\Middlewares;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;

class InvoicePreviewInertiaHandler extends HandleInertiaRequests
{
    public $rootView = 'invoice::app';

    public array $extraTranslations = ['invoices', 'catalog'];

    public function share(Request $request): array
    {
        $overrides = [];
        $isGuest = blank($request->user());
        if ($isGuest) {
            $overrides = [
                'user' => [
                    'locked' => false,
                    'impersonating' => false,
                    'photo' => null,
                    'tenant' => [
                        'name' => '',
                        'photo' => null,
                    ],
                    'isGuest' => true,
                ],
            ];
        }

        return array_merge(parent::share($request), [
            'title' => trans('invoices.labels.invoice-preview'),
            'isGuest' => $isGuest,
            ...$overrides,
        ]);
    }
}

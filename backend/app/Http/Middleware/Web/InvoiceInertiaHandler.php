<?php

namespace App\Http\Middleware\Web;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;

class InvoiceInertiaHandler extends HandleInertiaRequests
{
    public array $extraTranslations = ['invoices', 'order', 'catalog'];

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'title' => trans('headings.invoices'),
        ]);
    }
}

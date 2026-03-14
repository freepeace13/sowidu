<?php

namespace App\Http\Controllers\Inertia\Addressbook\Trash;

use App\Http\Controllers\Inertia\InertiaController;
use App\Http\Controllers\Traits\AddressbookTrait;
use App\Transformers\Addressbook\AddressbookTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AddressbookTrashController extends InertiaController
{
    use AddressbookTrait;

    public function __invoke(Request $request)
    {
        $service = $this->createServiceInstance();

        return Inertia::render('Addressbook/Trash/Index', [
            'addressbooks' => $service
                ->onlyTrashed()
                ->matchesText($request->get('q'))
                ->orderBy('name')
                ->filter($request->only(['initial']))
                ->get()
                ->map(
                    fn ($addressbook) => (new AddressbookTransformer($addressbook))->withModelType()->resolve(),
                ),
            // 'paginationItems' => $service->onlyTrashed()
            //     ->get('name')
            //     ->map(
            //         fn ($addressbook) => ucfirst(substr($addressbook->name, 0, 1))
            //     )->unique()->sort()->values()->toArray(),

            'filters' => $request->only(['initial', 'q']),
        ]);
    }
}

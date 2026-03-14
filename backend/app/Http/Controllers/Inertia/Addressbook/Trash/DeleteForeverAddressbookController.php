<?php

namespace App\Http\Controllers\Inertia\Addressbook\Trash;

use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Addressbook;

class DeleteForeverAddressbookController extends InertiaController
{
    public function __invoke($addressbook)
    {
        Addressbook::withTrashed()->findOrFail($addressbook)->forceDelete();

        return back(303);
    }
}

<?php

namespace App\Http\Controllers\Inertia\Addressbook\Trash;

use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Addressbook;

class RestoreAddressbookController extends InertiaController
{
    public function __invoke($addressbook)
    {
        Addressbook::withTrashed()->findOrFail($addressbook)->restore();

        return back(303);
    }
}

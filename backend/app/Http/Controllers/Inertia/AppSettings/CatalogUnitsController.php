<?php

namespace App\Http\Controllers\Inertia\AppSettings;

use App\Actions\AppSettings\Catalog\Unit\CreateCatalogUnit;
use App\Actions\AppSettings\Catalog\Unit\DeleteCatalogUnit;
use App\Actions\AppSettings\Catalog\Unit\UpdateCatalogUnit;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\CatalogItemUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogUnitsController extends InertiaController
{
    public function index()
    {
        return Inertia::render('AppSettings/Catalog/Units', [
            'units' => CatalogItemUnit::all()->toArray(),
        ]);
    }

    public function store(Request $request)
    {
        CreateCatalogUnit::run($request->user(), $request->all());

        flash_success('Catalog item unit has been created.');

        return back();
    }

    public function update(Request $request, CatalogItemUnit $unit)
    {
        UpdateCatalogUnit::run($request->user(), $unit, $request->all());

        flash_success('Catalog item unit has been updated.');

        return back();
    }

    public function destroy(Request $request, CatalogItemUnit $unit)
    {
        DeleteCatalogUnit::run($request->user(), $unit);

        flash_success('Catalog item unit has been deleted.');

        return back();
    }
}

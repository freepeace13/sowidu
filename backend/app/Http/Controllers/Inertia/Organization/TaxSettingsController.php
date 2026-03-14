<?php

namespace App\Http\Controllers\Inertia\Organization;

use App\Actions\Organization\Settings\Tax\CreateTax;
use App\Actions\Organization\Settings\Tax\DeleteTax;
use App\Actions\Organization\Settings\Tax\UpdateTax;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Tax;
use App\Transformers\CompanyTransformer;
use App\Transformers\TaxTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaxSettingsController extends InertiaController
{
    public function index(Request $request)
    {
        return Inertia::render(
            'Account/Organization/TaxSettings',
            [
                'taxes' => $request->company()
                    ->taxes()
                    ->orderBy('name')
                    ->get()
                    ->map(
                        fn (Tax $tax) => TaxTransformer::make($tax)
                            ->resolve(),
                    ),
                'company' => CompanyTransformer::make($request->company())
                    ->withTaxSettings()
                    ->resolve(),
            ],
        );
    }

    public function store(Request $request)
    {
        CreateTax::run($request->user(), $request->company(), $request->all());

        flash_success(__('account.tax.messages.created'));

        return back();
    }

    public function update(Request $request, Tax $tax)
    {
        UpdateTax::run(
            $request->user(),
            $request->company(),
            $tax,
            $request->all(),
        );

        flash_success(__('account.tax.messages.updated'));

        return back();
    }

    public function destroy(Request $request, Tax $tax)
    {
        DeleteTax::run(
            $request->user(),
            $request->company(),
            $tax,
        );

        flash_success(__('account.tax.messages.deleted'));

        return back();
    }
}

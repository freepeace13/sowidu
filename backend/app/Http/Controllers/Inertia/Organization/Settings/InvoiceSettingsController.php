<?php

namespace App\Http\Controllers\Inertia\Organization\Settings;

use App\Actions\Organization\Settings\UpdateCompanyInvoiceDefaults;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Tax;
use App\Services\AppServices;
use App\Transformers\CompanyTransformer;
use App\Transformers\EmployeeTransformer;
use App\Transformers\TaxTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceSettingsController extends InertiaController
{
    public function index(Request $request)
    {
        $company = $request->company();

        return Inertia::render(
            'Account/Organization/InvoiceSettings',
            [
                'taxes' => $company
                    ->taxes()
                    ->orderBy('name')
                    ->get()
                    ->map(
                        fn (Tax $tax) => TaxTransformer::make($tax)
                            ->resolve(),
                    ),

                'company' => CompanyTransformer::make($company)
                    ->withTaxSettings()
                    ->withInvoiceDefaults()
                    ->resolve(),

                'currencies' => AppServices::currencies(),

                'employees' => fn () => $company->employees()
                    ->with(['user'])
                    ->get()
                    ->map(
                        fn ($employee) => EmployeeTransformer::make($employee)
                            ->withRoles()
                            ->resolve(),
                    ),
            ],
        );

    }

    public function store(Request $request)
    {
        UpdateCompanyInvoiceDefaults::run(
            $request->user(),
            $request->company(),
            $request->all(),
        );

        flash_success(__('account.messages.invoice-settings.updated'));

        return back();
    }
}

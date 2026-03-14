<?php

namespace App\Http\Controllers\Inertia\Organization;

use App\Actions\Organization\Settings\UpdateCompanyTaxNumber;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateTaxNumberController extends Controller
{
    public function __invoke(Request $request)
    {
        UpdateCompanyTaxNumber::run(
            $request->user(),
            $request->company(),
            $request->all(),
        );

        flash_success(__('account.tax.messages.tax-number-updated'));

        return back();
    }
}

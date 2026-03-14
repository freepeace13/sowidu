<?php

namespace App\Http\Controllers\Inertia\Organization;

use App\Actions\Organization\Settings\UpdateCompanyVatIdentificationNumber;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateVatIdentificationNumberController extends Controller
{
    public function __invoke(Request $request)
    {
        UpdateCompanyVatIdentificationNumber::run(
            $request->user(),
            $request->company(),
            $request->all(),
        );

        flash_success(__('account.tax.messages.vat-identification-number-updated'));

        return back();
    }
}

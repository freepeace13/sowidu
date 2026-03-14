<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Modules\Invoice\Actions\UpdateInvoiceSubjectAndDescription;
use Illuminate\Http\Request;

class UpdateInvoicePreviewController extends Controller
{
    public function __invoke(Request $request, Invoice $invoice)
    {
        UpdateInvoiceSubjectAndDescription::run(
            $request->user(),
            $request->company(),
            $invoice,
            $request->all(),
        );

        flash_success(__('invoices.message.success.update-subject-or-description'));

        return back();
    }
}

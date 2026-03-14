<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeductionRequest;
use App\Models\Invoice;
use App\Models\InvoiceDeduction;
use App\Modules\Invoice\Actions\Deduction\AddInvoiceDeduction;
use App\Modules\Invoice\Actions\Deduction\AddInvoiceManualDeduction;
use App\Modules\Invoice\Actions\Deduction\RemoveInvoiceDeduction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InvoiceDeductionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function deduct(Request $request, Invoice $invoice): RedirectResponse
    {
        AddInvoiceDeduction::run($request->user(), $invoice, $request->all());

        flash_success(message: 'Successfully attached deductions');

        return back();
    }

    public function remove(
        Request $request,
        Invoice $invoice,
        InvoiceDeduction $invoiceDeduction,
    ): RedirectResponse {

        RemoveInvoiceDeduction::run(
            $request->user(),
            $request->company(),
            $invoice,
            $invoiceDeduction,
        );

        flash_success('Successfully detached deduction ' . $invoice->internal_id);

        return back();
    }

    public function manual(DeductionRequest $request, Invoice $invoice): RedirectResponse
    {
        AddInvoiceManualDeduction::run($request->user(), $invoice, $request->all());

        flash_success(message: 'Successfully attached deductions');

        return back();
    }
}

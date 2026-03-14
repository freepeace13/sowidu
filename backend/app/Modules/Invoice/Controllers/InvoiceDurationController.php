<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InvoiceDurationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Invoice $invoice): RedirectResponse
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $invoice->update([
            'execution_period_start' => $request->input('start'),
            'execution_period_end' => $request->input('end'),
        ]);

        flash_success(trans('invoices.message.duration.updated'));

        return back();
    }
}

<?php

namespace App\Http\Controllers\Json\Invoice;

use App\Http\Controllers\Json\BaseController;
use App\Models\Invoice;
use App\Modules\Invoice\Actions\Preview\SaveInvoicePreviewLayout;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Http\Request;

class InvoicePreviewLayoutController extends BaseController
{
    public function store(Request $request, Invoice $invoice)
    {
        SaveInvoicePreviewLayout::run($invoice, $request->all());

        return response()->json([
            'success' => true,
        ]);
    }

    public function destroy(Request $request, Invoice $invoice)
    {
        InvoiceService::run($invoice)->resetPreviewLayout();

        return response()->json([
            'success' => true,
        ]);
    }
}

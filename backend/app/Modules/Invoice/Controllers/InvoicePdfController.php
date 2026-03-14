<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Modules\Invoice\Actions\GenerateInvoicePdf;
use App\Modules\Invoice\Actions\GenerateInvoicePdfA;
use Illuminate\Http\Request;

class InvoicePdfController extends Controller
{
    public function stream(Request $request, $invoice)
    {
        $invoice = Invoice::findByIdentifierOrFail($invoice);

        $invoice->loadMissing(['company', 'client']);

        $pdf = GenerateInvoicePdf::run($invoice);

        return $pdf->stream($invoice->internal_id . '.pdf');
    }

    public function download(Request $request, $invoice)
    {
        $invoice = Invoice::findByIdentifierOrFail($invoice);

        $invoice->loadMissing(['company', 'client']);

        $filename = "{$invoice->internal_id}.pdf";

        if ($invoice->isDraft()) {
            $pdf = GenerateInvoicePdf::run($invoice);

            return $pdf->download($filename);
        }

        $pdfaPath = GenerateInvoicePdfA::run($invoice);

        while (ob_get_level()) {
            ob_end_clean();
        }

        return response()->download(
            $pdfaPath,
            $filename,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ],
        );
    }
}

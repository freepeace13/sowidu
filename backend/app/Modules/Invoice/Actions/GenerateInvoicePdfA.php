<?php

namespace App\Modules\Invoice\Actions;

use App\Actions\Traits\AsAction;
use App\Models\Invoice;
use App\Modules\Invoice\Services\InvoicePdfService;
use App\Modules\Invoice\Services\PdfaInvoiceBuilder;
use Illuminate\Support\Facades\File;

class GenerateInvoicePdfA
{
    use AsAction;

    public function handle(Invoice $invoice): string
    {
        $pdf = GenerateInvoicePdf::run($invoice);

        // If the invoice is not a draft, merge to be PDF/A
        $invoicePdfService = InvoicePdfService::make($invoice);
        $domPdfPath = $invoicePdfService->getDomPdfPath();
        $finalPdfPath = $invoicePdfService->getFullPath();

        $pdf->save($domPdfPath);

        // Merge
        PdfaInvoiceBuilder::make($invoice, $domPdfPath)
            ->setDestinationPath($finalPdfPath)
            ->merge();

        // Delete temporary PDF
        if (File::exists($domPdfPath)) {
            File::delete($domPdfPath);
        }

        return $finalPdfPath;
    }
}

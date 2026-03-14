<?php

namespace App\Modules\Invoice\Actions;

use App\Actions\Traits\AsAction;
use App\Models\Invoice;
use App\Modules\Invoice\Actions\Preview\GenerateInvoiceLineItemsTotals;
use App\Modules\Invoice\Actions\Preview\GetInvoiceLineItems;
use App\Modules\Invoice\InvoiceService;
use App\Transformers\InvoiceTransformer;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Canvas;
use Dompdf\FontMetrics;

class GenerateInvoicePdf
{
    use AsAction;

    public function handle(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $service = InvoiceService::run($invoice);

        $invoiceItems = GetInvoiceLineItems::run($invoice);
        $executionPeriod = $service->getExecutionPeriod();

        $startedAt = data_get($executionPeriod, 'started_at');
        $endedAt = data_get($executionPeriod, 'ended_at');

        $invoiceData = (new InvoiceTransformer($invoice))
            ->withStatus()
            ->withOrderFullDetails($invoice->order)
            ->withOrderClientDetails($invoice->order)
            ->withCompanyFullDetails($invoice->company)
            ->withExecutionPeriod($startedAt, $endedAt)
            ->withTotalWage($service->totalWage())
            ->withCareOf()
            ->withFormattedDates()
            ->withConstructionSite($service->getConstructionSite())
            ->toObject();

        $invoiceTotals = GenerateInvoiceLineItemsTotals::run($invoice);

        $pdf = Pdf::loadView(
            'invoice::pdf.base',
            [
                'invoice' => $invoiceData,
                'invoiceItems' => collect($invoiceItems)
                    ->map(fn ($item) => (object) $item)
                    ->values(),
                'client' => $invoiceData->client,
                'companyInvoiceDefaults' => $invoiceData->company->invoice_defaults,
                'company' => $invoiceData->company,
                'invoiceTotals' => $invoiceTotals,
            ],
        )->setOptions([
            'isRemoteEnabled' => true,
            'isPdfAEnabled' => true,
            'isPhpEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'fontHeightRatio' => 0.8,
            // Debugging
            // 'debugCss' => true,
            // 'debugLayout' => true,
            // 'debugLayoutLines' => true,
            // 'debugLayoutBlocks' => true,
            // 'debugLayoutInline' => true,
            // 'debugLayoutPaddingBox' => true,
        ], true)
            ->setPaper('a4')
            ->setWarnings(false);

        $dompdf = $pdf->getDomPDF();

        $dompdf->render();
        $canvas = $dompdf->getCanvas();

        // Add page numbers on top of every page
        $canvas->page_script(
            function ($pageNumber, $pageCount, Canvas $canvas, FontMetrics $fontMetrics) {
                if ($pageCount == 1) {
                    return;
                }

                $font = $fontMetrics->get_font('DejaVu Sans');
                $pageWidth = $canvas->get_width();

                $pageText = "Page {$pageNumber}";
                $fontSize = 6;

                $text_width = $fontMetrics->getTextWidth($pageText, $font, $fontSize);

                $x = ($pageWidth - $text_width) / 2;
                $y = 14;

                $canvas->text($x, $y, $pageText, $font, $fontSize);
            },
        );

        return $pdf;
    }
}

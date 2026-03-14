<?php

namespace Modules\Invoicify\Actions;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\File;
use Modules\Invoicify\Actions\Rules\GenerateInvoicePdfRules;
use Modules\Invoicify\Contracts\Actions\GeneratesInvoicePdf as GeneratesInvoicePdfContract;
use Modules\Invoicify\Facades\Pdf;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Services\PdfaInvoiceBuilder;
use Modules\Invoicify\Support\InvoicePdfFactory;
use Modules\Invoicify\Support\Pdf\PathGenerator;

class GenerateInvoicePdfAction implements GeneratesInvoicePdfContract
{
    use AuthorizesRequests;

    public function __construct(
        protected PathGenerator $pathGenerator,
    ) {}

    public function generate($user, Invoice $invoice, $teamId = null, $errorBag = null): string
    {
        $this->authorizeForUser($user, 'view', [$invoice, $teamId]);

        GenerateInvoicePdfRules::validate($invoice);

        $pdfView = InvoicePdfFactory::make($invoice);

        $tempPath = $this->pathGenerator->getTempPath($pdfView);

        File::ensureDirectoryExists(dirname($tempPath));

        $pdf = Pdf::loadComponent($pdfView);
        $pdf->save($tempPath);

        $finalPath = $this->pathGenerator->getPath($pdfView);

        // Ensure final directory exists before merging PDF
        File::ensureDirectoryExists(dirname($finalPath));

        PdfaInvoiceBuilder::make($invoice, $tempPath)
            ->setDestinationPath($finalPath)
            ->merge();

        return $finalPath;
    }
}

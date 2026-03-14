<?php

namespace Modules\Invoicify\Actions;

use App\Models\User;
use App\Rules\OwnedByCompany;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Modules\Invoicify\Contracts\Actions\BulkExportsInvoicePdfs;
use Modules\Invoicify\Contracts\Actions\GeneratesInvoicePdf;
use Modules\Invoicify\Events\PdfExportStarted;
use Modules\Invoicify\Jobs\CompressInvoicePdfsJob;
use Modules\Invoicify\Jobs\GenerateInvoicePdfJob;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Support\InvoicePdfFactory;
use Modules\Invoicify\Support\Pdf\PathGenerator;

class BulkExportInvoicePdfAction implements BulkExportsInvoicePdfs
{
    public function __construct(
        protected GeneratesInvoicePdf $generatesInvoicePdf,
        protected PathGenerator $pathGenerator,
    ) {}

    public function handle(User $user, array $inputs, $teamId = null, $errorBag = null): void
    {
        // Validate inputs
        $validated = $this->validate($inputs);
        $invoiceIdsRequested = $validated['invoice_ids'];

        // Load invoices efficiently with eager loading
        $invoices = Invoice::select(['id', 'company_id', 'user_id', 'internal_id'])
            ->whereIn('id', $invoiceIdsRequested)
            ->with(['company', 'client'])
            ->get();

        // Filter invoices that need PDF generation
        [$needsGeneration, $alreadyExists] = $invoices->partition(function ($invoice) {
            return !$this->pdfExists($invoice);
        });

        if ($needsGeneration->isNotEmpty()) {
            // Set cache flag for export in progress
            Cache::put(
                "user.{$user->id}.bulk_invoice_export",
                true,
                now()->addMinutes(5),
            );

            // Create jobs only for invoices needing generation
            $jobs = $needsGeneration->map(function ($invoice) use ($user, $teamId) {
                return new GenerateInvoicePdfJob($invoice, $user, $teamId);
            })->toArray();

            // Dispatch batch with finally callback
            $batch = Bus::batch($jobs)
                ->finally(function (Batch $batch) use ($invoiceIdsRequested, $user, $teamId) {
                    // Dispatch compression job when batch completes
                    dispatch(new CompressInvoicePdfsJob(
                        $invoiceIdsRequested,
                        $user,
                        $teamId,
                    ));

                    // Clear cache flag
                    Cache::put(
                        "user.{$user->id}.bulk_invoice_export",
                        false,
                        now()->addMinutes(5),
                    );
                })
                ->allowFailures()
                ->dispatch();

            // Broadcast export started event
            broadcast(new PdfExportStarted($user, $batch->toArray(), $teamId));
        } else {
            // All PDFs exist - skip batch and dispatch compression directly
            dispatch(new CompressInvoicePdfsJob(
                $invoiceIdsRequested,
                $user,
            ));
        }
    }

    public function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => [
                'required',
                'integer',
                'exists:invoices,id',
                new OwnedByCompany(Invoice::class),
            ],
        ])->validate();
    }

    /**
     * Get the expected PDF path for an invoice.
     */
    protected function getPdfPath(Invoice $invoice): string
    {
        // Ensure relationships are loaded for InvoicePdfFactory
        $invoice->loadMissing(['company', 'order', 'client']);

        $pdfView = InvoicePdfFactory::make($invoice);

        return $this->pathGenerator->getPath($pdfView);
    }

    /**
     * Check if PDF file exists for an invoice.
     */
    protected function pdfExists(Invoice $invoice): bool
    {
        try {
            return File::exists($this->getPdfPath($invoice));
        } catch (\Throwable $e) {
            // If PDF path generation fails (e.g., missing relationships), assume PDF doesn't exist
            return false;
        }
    }
}

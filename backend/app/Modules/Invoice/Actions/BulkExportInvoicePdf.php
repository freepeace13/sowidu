<?php

namespace App\Modules\Invoice\Actions;

use App\Actions\Traits\AsAction;
use App\Events\Invoice\ExportingPdfStarted;
use App\Jobs\Invoice\AutomateInvoicePdfDownloadJob;
use App\Jobs\Invoice\CompressInvoicePdfsJob;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use App\Rules\OwnedByCompany;
use App\Services\CacheService;
use App\Services\CompanyService;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Validator;

class BulkExportInvoicePdf
{
    use AsAction;

    public function handle(User $user, Company $company, array $inputs)
    {
        $requestedBy = $user;

        if ($company) {
            $requestedBy = CompanyService::make($company)
                ->getEmployeeFromUser($user);
        }

        // Validate the inputs
        $validated = $this->validate($inputs);

        $invoiceIdsRequested = $validated['invoice_ids'];

        // Generate the PDFs if not exists
        $invoices = collect($invoiceIdsRequested)
            ->map(function ($invoiceId) use ($requestedBy) {
                $invoice = Invoice::select(['id', 'company_id', 'user_id', 'internal_id'])->find($invoiceId);

                if (!$invoice) {
                    return null;
                }

                return new AutomateInvoicePdfDownloadJob(
                    $invoice,
                    $requestedBy,
                );
            })
            ->filter()
            ->values()
            ->toArray();

        if (filled($invoices)) {
            CacheService::userRequestedBulkInvoiceExport($user);

            // Dispatch batch
            $batch = Bus::batch($invoices)
                ->finally(function (Batch $batch) use ($invoiceIdsRequested, $requestedBy, $user) {
                    // Dispatch compressing all the PDFs and notify the user
                    dispatch(new CompressInvoicePdfsJob(
                        $invoiceIdsRequested,
                        $requestedBy,
                    ));

                    CacheService::userRequestedBulkInvoiceExportFinished($user);
                })
                ->allowFailures()
                ->dispatch();

            broadcast(new ExportingPdfStarted($requestedBy, $batch->toArray()));
        } else {
            dispatch(
                new CompressInvoicePdfsJob($invoiceIdsRequested, $requestedBy),
            );
        }

        return true; // or return the generated PDFs
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
}

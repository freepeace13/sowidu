<?php

namespace App\Jobs\Invoice;

use App\Events\Invoice\PdfExportProgressNotifier;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\User;
use App\Modules\Invoice\Actions\GenerateInvoicePdf;
use App\Modules\Invoice\Actions\GenerateInvoicePdfA;
use App\Modules\Invoice\Services\InvoicePdfService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutomateInvoicePdfDownloadJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Invoice $invoice,
        protected User|Employee $requester,
    ) {}

    public function handle()
    {
        try {
            sleep(1);

            $invoice = $this->invoice;

            $invoice->loadMissing(['company', 'client']);

            if ($invoice->isDraft()) {
                $invoicePdfService = InvoicePdfService::make($invoice);
                $path = $invoicePdfService->getFullPath();

                $pdf = GenerateInvoicePdf::run($invoice);
                $pdf->save($path);

                return;
            }

            // If the invoice is not a draft, merge to be PDF/A
            GenerateInvoicePdfA::run($invoice);

            return;
        } catch (\Throwable $e) {
            // logger('Error automating PDF download: ' . $e->getMessage());
        } finally {
            if ($batch = $this->batch()) {
                broadcast(new PdfExportProgressNotifier(
                    $this->requester,
                    [
                        'totalJobs' => $batch->totalJobs,
                        'pendingJobs' => $batch->pendingJobs,
                        'failedJobs' => $batch->failedJobs,
                        'id' => $batch->id,
                        'is_finished' => $batch->finished(),
                        'progress' => $batch->progress(),
                        'name' => $batch->name,
                    ],
                ));
            }
        }
    }
}

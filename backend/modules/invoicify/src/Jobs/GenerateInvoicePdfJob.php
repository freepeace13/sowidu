<?php

namespace Modules\Invoicify\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Invoicify\Contracts\Actions\GeneratesInvoicePdf;
use Modules\Invoicify\Events\PdfExportProgress;
use Modules\Invoicify\Models\Invoice;

class GenerateInvoicePdfJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Invoice $invoice,
        protected $user,
        protected $teamId = null,
    ) {}

    public function handle(GeneratesInvoicePdf $generatesInvoicePdf): void
    {
        try {
            $generatesInvoicePdf->generate($this->user, $this->invoice, $this->teamId);
        } catch (\Throwable $e) {
            // Let failures bubble for batch tracking
            throw $e;
        } finally {
            if ($batch = $this->batch()) {
                broadcast(new PdfExportProgress(
                    $this->user,
                    [
                        'totalJobs' => $batch->totalJobs,
                        'pendingJobs' => $batch->pendingJobs,
                        'failedJobs' => $batch->failedJobs,
                        'id' => $batch->id,
                        'is_finished' => $batch->finished(),
                        'finishedAt' => $batch->finishedAt?->toIso8601String(),
                        'progress' => $batch->progress(),
                        'name' => $batch->name,
                    ],
                    $this->teamId,
                ));
            }
        }
    }
}

<?php

namespace Modules\Invoicify\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Invoicify\Contracts\Actions\CompressesInvoicePdfs;
use Modules\Invoicify\Events\PdfExportCompleted;

class CompressInvoicePdfsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected array $invoiceIds,
        protected $requestedBy,
        protected ?int $teamId = null,
    ) {}

    public function handle(CompressesInvoicePdfs $compressInvoicePdfs): void
    {
        try {
            $result = $compressInvoicePdfs->compress(
                $this->requestedBy,
                $this->invoiceIds,
                $this->teamId,
            );

            broadcast(new PdfExportCompleted(
                $this->requestedBy,
                $result['file_url'],
                $result['file_name'],
                $this->teamId,
            ));

            // Send notification if user has notify method
            if (method_exists($this->requestedBy, 'notify')) {
                // Note: Notification class should be created in main app or module
                // For now, we'll just broadcast the event
            }
        } catch (\Exception $e) {
            logger()->error('Error compressing invoice PDFs: ' . $e->getMessage(), [
                'exception' => $e,
                'invoice_ids' => $this->invoiceIds,
            ]);
            throw $e;
        }
    }
}

<?php

namespace App\Jobs\Invoice;

use App\Events\Invoice\PdfExportCompleted;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\User;
use App\Modules\Invoice\Services\InvoicePdfService;
use App\Notifications\Invoice\BulkInvoiceExportCompletedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use STS\ZipStream\ZipStreamFacade;

class CompressInvoicePdfsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected array $invoiceIds,
        protected User|Employee $requestedBy,
    ) {}

    public function handle()
    {
        try {
            $zipFileName = implode('', [
                'invoices_',
                now()->unix(),
                Str::random(10),
                '.zip',
            ]);

            $zip = ZipStreamFacade::create($zipFileName);

            // Add PDF's to the zip
            foreach ($this->invoiceIds as $invoiceId) {
                $invoice = Invoice::select(['id', 'internal_id'])->find($invoiceId);
                $pdfPath = InvoicePdfService::make($invoice)
                    ->getStoragePath();

                $storage = Storage::disk('public');

                if (!$storage->exists($pdfPath)) {
                    continue;
                }

                $zip->add(
                    Storage::disk('public')->path($pdfPath),
                );
            }

            $fileDir = 'zips/invoices';

            $zip->saveTo(Storage::disk('public')->path($fileDir));

            $fileUrl = Storage::disk('public')
                ->url("$fileDir/$zipFileName");

            broadcast(new PdfExportCompleted(
                $this->requestedBy,
                $fileUrl,
                $zipFileName,
            ));

            $this->requestedBy->notify(
                new BulkInvoiceExportCompletedNotification(
                    $fileUrl,
                ),
            );

        } catch (\Exception $e) {
            // Handle the exception
            logger('Error compressing invoice PDFs: ' . $e->getMessage());
        }
    }
}

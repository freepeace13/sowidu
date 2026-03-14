<?php

namespace App\Console\Commands;

use App\Events\Invoice\ExportingPdfStarted;
use App\Jobs\Invoice\AutomateInvoicePdfDownloadJob;
use App\Jobs\Invoice\InvoicePreviewSnapshotJob;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Offer;
use App\Modules\Invoice\Actions\BulkExportInvoicePdf;
use App\Modules\Invoice\Services\InvoicePdfService;
use App\Modules\Offer\Events\OfferSent;
use App\Notifications\Invoice\BulkInvoiceExportCompletedNotification;
use App\Transformers\CompanyTransformer;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Throwable;

class AppTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $offer = Offer::find(105);
        event(new OfferSent($offer));
        dd('done');
        // 169 164 145 138 130 125
        // Invoice::all()
        //     ->each(fn ($invoice) => $invoice->update([
        //         'preview_layout' => null,
        //     ]));
        // dd(
        //     Invoice::whereNotNull('preview_layout')->count(),
        // );

        $offer = Offer::find(90);

        event(new OfferSent($offer));
        dd();

        $invoice = Invoice::find(169);
        $company = Company::find(2001);
        $owner = $company->founder;

        // dispatch_sync(new InvoicePreviewSnapshotJob($invoice, $owner));
        dispatch_sync(new AutomateInvoicePdfDownloadJob($invoice, $owner));

        dd('done', InvoicePdfService::make($invoice)->hasPdf());

        // $invoice->update([
        //     'preview_layout' => null,
        // ]);

        // dispatch_sync(new InvoicePreviewSnapshotJob($invoice, $owner));
        dispatch_sync(new AutomateInvoicePdfDownloadJob($invoice, $owner));

        dd('done');

        $batch = Bus::batch([])
            ->name('exporting.invoice.pdf-' . $owner->broadcastChannel())
            ->allowFailures()
            ->dispatch();

        broadcast(new ExportingPdfStarted($owner, $batch->toArray()));
        // $owner->notify(new ExportingPdfStarted($owner));

        // dd(CompanyTransformer::make($company)->resolve());
        dd(
            // $owner->notify(new BulkInvoiceExportCompletedNotification(
            //     'http://app.sowidu.test/zips/invoices/invoices_1744207620KVIKnU2Nu8.zip',
            // )),
        );
        BulkExportInvoicePdf::run($owner->user()
            ->first(), $company, [
                'invoice_ids' => [127, 176, 175],
            ]);
        dd('done');
        $ids = [70, 176, 175];

        dispatch_sync(new \App\Jobs\Invoice\CompressInvoicePdfsJob($ids, $owner));
        dd('done');
        $invoice = Invoice::whereNull('preview_layout')->first();

        // $invoice = Invoice::find(61);
        // $invoice->update(['preview_layout' => null]);
        $jobs = collect();
        Invoice::query()
            ->chunk(10, function ($invoices) use ($jobs) {
                $invoiceJobs = $invoices->map(function ($invoice) {});

                $jobs->push(...$invoiceJobs);
            });

        // Batch
        $batch = Bus::batch($jobs->all())
            ->catch(function (Batch $batch, Throwable $e) {
                // First batch job failure detected...
                logger()->error('Batch failed', [
                    'batch' => $batch,
                    'exception' => $e,
                ]);

            })
            ->finally(function (Batch $batch) {
                // The batch has finished executing...
                logger()->info('Batch finished', [
                    'batch' => $batch,
                ]);

            })
            ->allowFailures()
            ->dispatch();

        return Command::SUCCESS;
    }
}

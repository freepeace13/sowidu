<?php

namespace Modules\Invoicify\Actions;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Invoicify\Contracts\Actions\CompressesInvoicePdfs;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Support\InvoicePdfFactory;
use Modules\Invoicify\Support\Pdf\PathGenerator;
use STS\ZipStream\ZipStreamFacade;

class CompressInvoicePdfsAction implements CompressesInvoicePdfs
{
    use AuthorizesRequests;

    public function __construct(
        protected PathGenerator $pathGenerator,
    ) {}

    public function compress(User $user, array $invoiceIds, $teamId = null, $errorBag = ''): array
    {
        // Validate invoice IDs
        $this->validate($invoiceIds, $errorBag);

        // Load invoices with required relationships
        $invoices = Invoice::whereIn('id', $invoiceIds)
            ->select(['id', 'internal_id', 'company_id', 'order_id', 'user_id'])
            ->with(['company', 'order', 'client'])
            ->get();

        // Authorize access to all invoices
        foreach ($invoices as $invoice) {
            $this->authorizeForUser($user, 'view', [$invoice, $teamId]);
        }

        // Generate zip filename
        $zipFileName = implode('', [
            'invoices_',
            now()->unix(),
            Str::random(10),
            '.zip',
        ]);

        // Create zip stream
        $zip = ZipStreamFacade::create($zipFileName);

        // Add PDFs to the zip
        foreach ($invoices as $invoice) {
            $pdfView = InvoicePdfFactory::make($invoice);
            $absolutePath = $this->pathGenerator->getPath($pdfView);

            // Skip if PDF doesn't exist
            if (!File::exists($absolutePath)) {
                continue;
            }

            $zip->add($absolutePath);
        }

        // Save zip file
        $fileDir = 'zips/invoices';
        Storage::disk('public')->makeDirectory($fileDir);

        $filePath = Storage::disk('public')->path("$fileDir/$zipFileName");
        $zip->saveTo(Storage::disk('public')->path($fileDir));

        // Generate file URL
        $fileUrl = Storage::disk('public')
            ->url("$fileDir/$zipFileName");

        return [
            'file_url' => $fileUrl,
            'file_name' => $zipFileName,
            'file_path' => $filePath,
        ];
    }

    protected function validate(array $invoiceIds, $errorBag = ''): void
    {
        Validator::make(
            ['invoice_ids' => $invoiceIds],
            [
                'invoice_ids' => 'required|array',
                'invoice_ids.*' => [
                    'required',
                    'integer',
                    'exists:invoices,id',
                ],
            ],
            [],
            ['invoice_ids.*' => 'invoice'],
        )->validateWithBag($errorBag);
    }
}

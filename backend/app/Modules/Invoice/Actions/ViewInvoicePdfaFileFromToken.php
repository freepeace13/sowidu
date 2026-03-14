<?php

namespace App\Modules\Invoice\Actions;

use App\Actions\Traits\AsAction;
use App\Jobs\Invoice\DeletePdfaFileOnStorageJob;
use Illuminate\Support\Facades\Storage;

class ViewInvoicePdfaFileFromToken
{
    use AsAction;

    public function handle(string $token)
    {
        $fileName = cache()->get("pdf_token:{$token}");

        if (!$fileName) {
            abort(404, 'Invalid or expired token');
        }

        $pdfaFile = "invoices/$fileName";

        if (!Storage::disk('public')->exists($pdfaFile)) {
            abort(404, 'File not found');
        }

        // Schedule the file for deletion after 1 hour
        // dispatch(
        //     new DeletePdfaFileOnStorageJob($fileName, $token),
        // )->delay(now()->addHour());

        return Storage::disk('public')->url($pdfaFile);
    }
}

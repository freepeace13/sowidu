<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Invoice\Actions\BulkExportInvoicePdf;
use App\Services\CacheService;
use Illuminate\Http\Request;

class BulkInvoicePdfExportController extends Controller
{
    public function __invoke(Request $request)
    {
        if (CacheService::hasUserRequestedBulkInvoiceExport($request->user())) {
            flash_error(__('invoices.notifications.employee.bulk-export-is-busy'));

            return back();
        }

        BulkExportInvoicePdf::run($request->user(), $request->company(), $request->all());

        flash_success(__('invoices.notifications.employee.bulk-export-started'));

        return back();
    }
}

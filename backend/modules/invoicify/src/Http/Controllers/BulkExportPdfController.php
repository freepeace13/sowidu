<?php

namespace Modules\Invoicify\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Invoicify\Contracts\Actions\BulkExportsInvoicePdfs;
use Modules\Shared\Http\Controllers\InertiaController;

class BulkExportPdfController extends InertiaController
{
    public function __invoke(Request $request, BulkExportsInvoicePdfs $action)
    {
        $user = $request->user();
        $teamId = $user->currentTeam()?->id;

        $action->handle($user, $request->all(), $teamId);

        flash_success(__('invoices.notifications.employee.bulk-export-started'));

        return back();
    }
}

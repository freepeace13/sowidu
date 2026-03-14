<?php

namespace Modules\Invoicify\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Invoicify\Contracts\Actions\GeneratesInvoicePdf;
use Modules\Invoicify\Models\Invoice;

class ExportPdfController
{
    public function __invoke(GeneratesInvoicePdf $action, Request $request, Invoice $invoice)
    {
        $user = $request->user();
        $teamId = $user->currentTeam()?->id;

        $pdfPath = $action->generate($user, $invoice, $teamId);

        return response()->download($pdfPath);
    }
}

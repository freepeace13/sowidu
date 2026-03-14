<?php

namespace Modules\Invoicify\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Invoicify\Contracts\Actions\MarksInvoicesAsPaid;
use Modules\Invoicify\Models\Invoice;
use Modules\Shared\Http\Controllers\InertiaController;

class MarkAsPaidController extends InertiaController
{
    public function __invoke(Request $request, Invoice $invoice, MarksInvoicesAsPaid $marker)
    {
        $user = $request->user();
        $teamId = $user->currentTeam()?->id;

        try {
            $marker->markAsPaid($user, $invoice, $teamId);

            flash_success(trans('invoices.message.mark-as-paid'));
        } catch (\Exception $e) {
            flash_error($e->getMessage());

            throw $e;
        }

        return back();
    }
}

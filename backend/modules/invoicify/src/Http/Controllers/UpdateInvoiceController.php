<?php

namespace Modules\Invoicify\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Invoicify\Contracts\Actions\UpdatesInvoices;
use Modules\Invoicify\Models\Invoice;
use Modules\Shared\Http\Controllers\InertiaController;

class UpdateInvoiceController extends InertiaController
{
    public function __invoke(Request $request, Invoice $invoice, UpdatesInvoices $action)
    {
        $user = $request->user();
        $teamId = $user->currentTeam()?->id;

        $action->update($user, $invoice, $request->all(), $teamId);

        flash_success(trans('invoices.message.success.updating'));

        return back();
    }
}

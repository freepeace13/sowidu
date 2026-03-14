<?php

namespace Modules\Invoicify\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Invoicify\Contracts\Actions\UpdatesInvoiceManualItems;
use Modules\Invoicify\Models\InvoiceManualItem;
use Modules\Shared\Http\Controllers\InertiaController;

class UpdateManualItemController extends InertiaController
{
    public function __invoke(Request $request, InvoiceManualItem $manualItem, UpdatesInvoiceManualItems $updater)
    {
        $user = $request->user();
        $teamId = $user->currentTeam()?->id;

        // $updater->update($user, $manualItem, $request->all(), $teamId);

        flash_success(__('invoices.manual-item.messages.manual-item-updated'));

        return back();
    }
}

<?php

namespace Modules\Invoicify\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Invoicify\Contracts\Actions\AddsInvoiceManualItems;
use Modules\Invoicify\Models\Invoice;
use Modules\Shared\Http\Controllers\InertiaController;

class StoreManualItemController extends InertiaController
{
    public function __invoke(Request $request, Invoice $invoice, AddsInvoiceManualItems $adder)
    {
        $user = $request->user();
        $company = $request->company();

        try {
            $adder->add($user, $invoice, $request->all(), $company);

            flash_success(__('invoices.manual-item.messages.manual-item-added'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            flash_error($e->getMessage());
            throw $e;
        }

        return back();
    }
}

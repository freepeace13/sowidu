<?php

namespace Modules\Invoicify\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Invoicify\Contracts\Actions\SendsInvoiceToClient;
use Modules\Invoicify\Models\Invoice;
use Modules\Shared\Http\Controllers\InertiaController;

class SendToClientController extends InertiaController
{
    public function __invoke(Request $request, Invoice $invoice, SendsInvoiceToClient $sender)
    {
        $user = $request->user();
        $teamId = $user->currentTeam()?->id;

        try {
            $sender->send($user, $invoice, $teamId);

            flash_success(trans('invoices.message.success.sending-to-client'));
        } catch (\Exception $e) {
            flash_error($e->getMessage());

            throw $e;
        }

        return back()->with('flash', [
            'preview_open' => true,
            'type' => 'success',
            'message' => trans('invoices.message.success.sending-to-client'),
        ]);
    }
}

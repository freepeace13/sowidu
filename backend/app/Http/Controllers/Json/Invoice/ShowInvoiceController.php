<?php

namespace App\Http\Controllers\Json\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Transformers\InvoiceTransformer;
use Illuminate\Http\Request;

class ShowInvoiceController extends Controller
{
    public function __invoke(Request $request, Invoice $invoice)
    {
        abort_if(
            !$invoice->isOwnedByCompany($request->company()),
            403,
            trans('validation.403'),
        );
        $transformer = (new InvoiceTransformer($invoice->loadMissing([
            'documents.media',
        ])))
            ->withBillerDetails()
            ->withOrderFullDetails($invoice->order)
            ->withDocuments()
            ->withOrderClientDetails($invoice->order)
            ->withDeliveryAddress();

        return response()->json($transformer->resolve());
    }
}

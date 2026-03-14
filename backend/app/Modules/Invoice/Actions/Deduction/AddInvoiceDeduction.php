<?php

namespace App\Modules\Invoice\Actions\Deduction;

use App\Actions\Traits\AsAction;
use App\Models\Invoice;
use App\Models\User;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Support\Facades\Validator;

class AddInvoiceDeduction
{
    use AsAction;

    public function handle(User $user, Invoice $invoice, array $inputs)
    {
        // TODO - check if user allowed to add deduction to invoice!

        $validated = $this->validate($inputs);

        $invoiceIds = data_get($validated, 'invoices', []);

        // Validate if invoice chosen is already added as deduction to other invoice
        foreach ($invoiceIds as $invoiceId) {
            throw_validation_if(
                $invoice->deductions()
                    ->isDeductible(
                        Invoice::findOrFail($invoiceId),
                    )
                    ->exists(),
                trans('invoices.message.failed.invoice-already-a-deduction'),
            );
        }

        // TODO - remove `deduction_invoice_id` from invoices
        // Invoice::whereIn('id', $invoiceIds)
        //     ->get()
        //     ->each(function ($deductable) use ($invoice) {
        //         $deductable->update([
        //             'deduction_invoice_id' => $invoice->id,
        //         ]);
        //     });

        // Using `invoice_deductions` table
        InvoiceService::run($invoice)->attachInvoiceDeductions($invoiceIds);

        return $invoice;
    }

    public function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'invoices' => 'array',
        ])->validate();
    }
}

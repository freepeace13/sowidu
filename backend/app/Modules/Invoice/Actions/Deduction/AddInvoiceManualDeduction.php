<?php

namespace App\Modules\Invoice\Actions\Deduction;

use App\Actions\Traits\AsAction;
use App\Models\DeductionManual;
use App\Models\Invoice;
use App\Models\User;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Support\Facades\Validator;

class AddInvoiceManualDeduction
{
    use AsAction;

    public function handle(User $user, Invoice $invoice, array $inputs): DeductionManual
    {
        // TODO - check if user allowed to add deduction to invoice!

        $validated = $this->validated($inputs);

        $manualDeduction = $invoice->manualDeductions()
            ->create($validated);

        InvoiceService::run($invoice)->attachDeduction($manualDeduction);

        return $manualDeduction;
    }

    protected function validated(array $inputs)
    {
        return Validator::make($inputs, [
            'amount' => ['required', 'numeric'],
            'operator' => ['required', 'string'],
            'name' => ['required', 'string'],
        ])->validate();
    }
}

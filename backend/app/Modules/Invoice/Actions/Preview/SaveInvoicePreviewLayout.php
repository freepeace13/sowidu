<?php

namespace App\Modules\Invoice\Actions\Preview;

use App\Actions\Traits\AsAction;
use App\Models\Invoice;
use Illuminate\Support\Facades\Validator;

class SaveInvoicePreviewLayout
{
    use AsAction;

    public function handle(Invoice $invoice, array $inputs)
    {
        $validated = $this->validate($inputs);
        $invoice->update([
            'preview_layout' => $validated['preview_layout'],
        ]);

        return $invoice;
    }

    protected function validate(array $inputs): array
    {
        return Validator::make($inputs, [
            'preview_layout' => ['required', 'array'],
        ])->validate();
    }
}

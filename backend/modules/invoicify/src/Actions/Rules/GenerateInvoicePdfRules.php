<?php

namespace Modules\Invoicify\Actions\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Invoicify\Models\Invoice;

class GenerateInvoicePdfRules
{
    public static function validate(Invoice $invoice): void
    {
        // Load the order relationship if not already loaded
        if (!$invoice->relationLoaded('order')) {
            $invoice->load('order');
        }

        $orderNumber = $invoice->order?->order_number;

        // Check if order_number is null, empty string, or order doesn't exist
        if (!$invoice->order || empty($orderNumber)) {
            $validator = Validator::make(
                ['order_number' => $orderNumber],
                ['order_number' => ['required', 'filled']],
                ['order_number.required' => __('invoices.message.failed.reuired_order_number')],
            );

            throw new ValidationException($validator);
        }
    }
}

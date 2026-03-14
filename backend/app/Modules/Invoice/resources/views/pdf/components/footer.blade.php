<div class="invoice-footer">
    <div class="invoice-footer-container tw-grid tw-grid-cols-2 tw-w-full">
        <div class="flex tw-flex tw-text-xs bank-name tw-col-span-2">
            <div>{{ __('invoices.preview.bank-details') }}:</div>
            <div class="ml-1"> {{ $companyInvoiceDefaults->bank_name  }} </div>
        </div>
        <div class="flex tw-flex tw-text-xs iban-bic tw-flex-col tw-gap-y-2 xs4">
            <div class="iban tw-flex">
                <div>{{ __('invoices.preview.iban') }}:</div>
                <div class="ml-1"> {{ $companyInvoiceDefaults->iban }} </div>
            </div>
            <div class="bic tw-flex">
                <div>{{ __('invoices.preview.bic') }}:</div>
                <div class="ml-1"> {{ $companyInvoiceDefaults->bic }} </div>
            </div>
        </div>
        <div class="flex tw-flex tw-text-xs iban-bic tw-flex-col tw-gap-y-2 xs4">
            <div class="iban tw-flex">
                <div>{{ __('invoices.tax.labels.vat-identification-number') }}:</div>
                <div class="ml-1"> {{ $invoice->company->vat_identification_number  }} </div>
            </div>
            <div class="bic tw-flex">
                @if ($companyInvoiceDefaults->commercial_register && $companyInvoiceDefaults->commercial_register_number)
                    <div>
                        {{ $companyInvoiceDefaults->commercial_register }}
                        {{ __('invoices.preview.commercial-register-number') }}:
                    </div>
                    <div class="ml-1"> {{ $companyInvoiceDefaults->commercial_register_number }} </div>
                @endif
            </div>
        </div>
        <div class="tw-text-xs !tw-text-right managing-director xs4">
            <div> {{ __('invoices.preview.managing-director') }}: </div>
            <div class="tw-mt-2"> {{ $companyInvoiceDefaults->managing_director->name  }} </div>
        </div>
    </div>
</div>
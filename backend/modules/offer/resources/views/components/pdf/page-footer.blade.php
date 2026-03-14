@props(['companyInvoiceDefaults', 'company'])

<x-offer::pdf.layouts.footer name="defaultFooter">
    <x-offer::pdf.table class="simple">
        <tr>
            <td width="33%" class="nowrap" style="margin-right:6px;">
                <strong>Bank:</strong> {{ $companyInvoiceDefaults->bank_name ?? '' }}
            </td>
            <td width="33%" class="nowrap">
                <strong>VAT #:</strong> {{ $company->vat_identification_number ?? '' }}
            </td>
            <td width="33%" class="nowrap">
                @if ($companyInvoiceDefaults->commercial_register && $companyInvoiceDefaults->commercial_register_number)
                    <strong>HRB #:</strong> {{ $companyInvoiceDefaults->commercial_register_number }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="nowrap" style="margin-right:6px;">
                <strong>{{ __('invoices.preview.iban') }}:</strong> {{ $companyInvoiceDefaults->iban ?? '' }}
            </td>
            <td class="nowrap">
                <strong>{{ __('invoices.preview.bic') }}:</strong> {{ $companyInvoiceDefaults->bic ?? '' }}
            </td>
            <td class="nowrap">
                <strong>{{ __('invoices.preview.managing-director') }}:</strong> {{ $companyInvoiceDefaults?->managing_director?->name ?? '' }}
            </td>
        </tr>
    </x-offer::pdf.table>
</x-offer::pdf.layouts.footer>

@props(['paymentDetails'])

<x-invoicify::pdf.layouts.footer name="defaultFooter">
    <x-invoicify::pdf.table>
        <tr>
            <td width="33%" class="nowrap" style="margin-right:6px;">
                <strong>Bank:</strong> {{ $paymentDetails->bankName }}
            </td>
            <td width="33%" class="nowrap">
                <strong>VAT #:</strong> {{ $paymentDetails->vat }}
            </td>
            <td width="33%" class="nowrap">
                <strong>HRB #:</strong> {{ $paymentDetails->hrb }}
            </td>
        </tr>
        <tr>
            <td class=" nowrap" style="margin-right:6px;">
                <strong>{{ __('invoices.preview.iban') }}:</strong> {{ $paymentDetails->iban }}
            </td>
            <td class="nowrap">
                <strong>{{ __('invoices.preview.bic') }}:</strong> {{ $paymentDetails->bic }}
            </td>
            <td class="nowrap">
                <strong>{{ __('invoices.preview.managing-director') }}:</strong> {{ $paymentDetails->managingDirector }}
            </td>
        </tr>
    </x-invoicify::pdf.table>
</x-invoicify::pdf.layouts.footer>

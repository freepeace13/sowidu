@props(['itemTable'])

<x-invoicify::pdf.table style="margin-bottom:5mm; width:100%;">
    <x-slot:header>
        <th align="center" style="white-space: nowrap;">{{ __('invoices.preview.table.pos') }}</th>
        <th style="white-space: nowrap;">{{ __('invoices.preview.table.item-count') }}</th>
        <th style="white-space: nowrap;">{{ __('invoices.preview.table.item-unit') }}</th>
        <th align="left" style="white-space: nowrap;">{{ __('invoices.preview.table.item-description') }}</th>
        <th align="right" style="white-space: nowrap;">{{ __('invoices.preview.table.price-per-piece') }}</th>
        <th align="right" style="white-space: nowrap;">{{ __('invoices.preview.table.total-price') }}</th>
    </x-slot:header>

    @foreach ($itemTable->items as $item)
        <tr style="page-break-inside: auto;">
            <td align="center">
                {{ $item->sequenceNo }}
            </td>
            <td align="center">
                {{ $item->quantity }}
            </td>
            <td align="center">
                {{ $item->unit }}
            </td>
            <td>
                {{ $item->description }}
            </td>
            <td align="right">
                {{ $item->unitPrice }}
            </td>
            <td align="right">
                {{ $item->totalPrice }}
            </td>
        </tr>
    @endforeach
</x-invoicify::pdf.table>

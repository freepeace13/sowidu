@props(['totalsSummary'])

<x-invoicify::pdf.table class="simple" style="page-break-inside:avoid;">
    @php
        $grandTotals = last($totalsSummary);
        $totalsSummary = collect($totalsSummary)->slice(0, -1)->toArray();
    @endphp

    @foreach ($totalsSummary as $totals)
        @foreach ($totals as $item)
            <tr>
                <td align="right" width="70%" valign="top">
                    {{ $item['prefix'] ?? '' }} {{ $item['label'] }} :
                </td>
                <td align="right" width="30%">
                    {{ $item['value'] }}
                </td>
            </tr>
        @endforeach

        <tr>
            <td colspan="2" style="border-bottom: 1px solid #000; height: 16px; text-align: right;">
                <hr style="width: 30%; text-align: right; color: black;">
            </td>
        </tr>
    @endforeach

    @foreach ($grandTotals as $item)
        <tr>
            <td align="right" width="70%" valign="top">
                <b>{{ $item['prefix'] ?? '' }} {{ $item['label'] }} :</b>
            </td>
            <td align="right" width="30%">
                {{ $item['value'] }}
            </td>
        </tr>
    @endforeach

    <tr>
        <td width="70%"></td>
        <td width="30%">
            <hr style="text-align: right; color: black; height: 1px; margin-top: 0; margin-bottom: 1mm;">
            <hr style="text-align: right; color: black; height: 1px; margin-top: 0; margin-bottom: 0;">
        </td>
    </tr>
</x-invoicify::pdf.table>

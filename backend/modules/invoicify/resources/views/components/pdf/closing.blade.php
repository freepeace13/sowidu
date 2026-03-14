@props(['paymentDate', 'totalWageCosts', 'remarks'])

<div style="line-height:2;page-break-inside:avoid;color:white;">
    <strong>{{ __('invoices.labels.payment-date') }}: </strong> {{ $paymentDate }} <br>
    <strong>{{ __('invoices.labels.total-wage') }}: </strong> {{ $totalWageCosts }} <br>

    @if (filled($remarks))
        {{ $remarks }}
    @endif
</div>

<div style="position:absolute;bottom:35mm;left:15mm;right:15mm;">
    <div style="line-height: 2;">
        <strong>{{ __('invoices.labels.payment-date') }}: </strong>{{ $paymentDate }} <br>
        <strong>{{ __('invoices.labels.total-wage') }}: </strong>{{ $totalWageCosts }} <br>
    </div>

    @if (filled($remarks))
        <span style="line-height:normal;margin-bottom:2px;">
            {{ $remarks }}
        </span>
    @endif
</div>

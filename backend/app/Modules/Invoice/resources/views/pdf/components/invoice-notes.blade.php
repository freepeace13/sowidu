{{-- Invoice Notes --}}
@if (filled($invoice->payment_date))
    <div class="payment-date">
        <div class="tw-font-semibold">
            {{ __('invoices.labels.payment-date') }}:
        </div>
        <div> {{ $invoice->payment_date }} </div>
    </div>
@endif

@if ($invoice->total_wage > 0)
    <div class="total-wage">
        <div class="tw-font-semibold">
            {{ __('invoices.labels.total-wage') }}:
        </div>
        <div>
            {{ $invoice->total_wage_formatted }}
        </div>
    </div>
@endif
@if ($invoice->notes)
    <div class="notes">
        {!! nl2br(e($invoice->notes)) !!}
    </div>
@endif

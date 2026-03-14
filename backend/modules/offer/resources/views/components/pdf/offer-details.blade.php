@props(['offer', 'recipient'])

<div style="clear:both;margin-bottom:3mm;">
    <div style="float:left;width:33%;">
        <strong>{{ __('offer.labels.offer') }}: </strong> <br> {{ $offer->internal_id ?? $offer->external_id ?? '' }}
    </div>
    <div style="float:left;width:33%;" align="center">
        <strong>{{ __('order.labels.order-number') }}: </strong> <br> {{ $offer?->order?->order_number ?? '--' }}
    </div>
    <div style="float:left;width:33%;" align="right">
        <strong>{{ __('labels.date') }}: </strong> <br> {{ $offer->offer_date_formatted ?? $offer->updated_at_formatted ?? '' }}
    </div>
</div>

<div style="margin-bottom:3mm;">
    <strong>{{ __('invoices.preview.construction-site') }}: </strong> {{ $offer?->construction_site?->short_full_address ?? '' }} <br>
    <strong>{{ __('invoices.preview.execution-period') }}: </strong> {{ $offer->execution_period_start_formatted ?? '' }} - {{ $offer->execution_period_end_formatted ?? '' }} <br>
    <strong>{{ __('invoices.preview.service-recipient') }}: </strong> {{ $recipient->service_recipient ?? '' }} {{ $recipient->legal_form ?? '' }} <br>
</div>

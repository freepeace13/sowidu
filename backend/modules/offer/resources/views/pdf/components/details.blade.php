<div class="invoice-preview-information">
    <div class="main">
        <div class="invoice-id">
            <div class="tw-text-xs tw-font-semibold"> {{ __('offer.labels.offer') }}: </div>
            <div class="tw-text-base">
                {{ $offer->internal_id ?? $offer->external_id ?? '' }}
            </div>
        </div>
        <div class="order-number-date">
            <div class="tw-pr-2 order-number">
                <div class="tw-text-xs tw-font-semibold"> {{ __('order.labels.order-number') }}: </div>
                <div class="tw-text-base">
                    {{ $offer?->order?->order_number ?? '--' }}
                </div>
            </div>

            <div class="tw-ml-auto invoice-date">
                <div class="tw-font-semibold tw-text-xs"> {{ __('labels.date') }}: </div>
                <div class="tw-text-right tw-text-base">
                    {{ $offer->offer_date_formatted ?? $offer->updated_at_formatted ?? '' }}
                </div>
            </div>
        </div>

    </div>
    <div class="tw-text-xs construction-site">
        <div class="flex xs2"> {{ __('invoices.preview.construction-site') }}: </div>
        <div class="flex tw-text-xs xs6 pt-0">
            {{ $offer?->construction_site?->short_full_address ?? '' }}
        </div>
    </div>
    <div class=" tw-text-xs tw-leading-3 execution-period row wrap">
        <div class="flex xs2 pt-0"> {{ __('invoices.preview.execution-period') }}: </div>
        <div class="flex xs6 pt-0 tw-flex tw-gap-x-2">
            <div> {{ $offer->execution_period_start_formatted ?? '' }} </div>
            <div>-</div>
            <div> {{ $offer->execution_period_end_formatted ?? '' }} </div>
        </div>
    </div>
    <div class=" tw-text-xs service-recipient row wrap">
        <div class="xs2 py-0"> {{ __('invoices.preview.service-recipient') }}: </div>
        <div class="tw-text-xs xs6 py-0">
            {{ $recipient->service_recipient ?? '' }}
        </div>
    </div>
</div>

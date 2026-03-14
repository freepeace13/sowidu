@component('mail::message')
# {{ config('app.name') }}

Hi {{ $order->client?->name ?? $order->client?->full_name }},

<br />
{{ trans('invoices.mail.paid.intro', [
            'invoice' => $invoice->internal_id,
            'amount' => $totalAmount
        ]) }}
<br />

{{ trans('invoices.mail.paid.view-invoice') }}:

@component('mail::button', ['url' => route('orders.show.invoices.show', [
'invoice' => $invoice,
'order' => $order,
])])
{{ route('orders.show.invoices.show', [
            'invoice' => $invoice,
            'order' => $order,
        ])}}
@endcomponent

{{ trans('invoices.mail.footer') }},
<br />
{{ ucfirst($invoice->company->name) }}

@endcomponent

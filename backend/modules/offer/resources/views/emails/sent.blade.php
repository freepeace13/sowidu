@component('mail::message')
Hi {{ $recipientName }},

<br>
@if (blank($offer->message))
You have received a new offer from **{{ $sender }}**. <br> <br>
@else
{{ $offer->message }} <br> <br>
@endif

Please find attached a PDF copy of the offer. You can also view it online by clicking the button below.

@component('mail::button', ['url' => $url])
View Offer
@endcomponent

@if ($note)
"{{ $note }}" <br> <br>
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent

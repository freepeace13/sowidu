@component('mail::message')
# {{ config('app.name') }}

Hi {{ $nickname }},

You have been invited to join <strong>{{ $sender }}</strong>. <br> <br>

@if ($note)
"{{ $note }}" <br> <br>
@endif

@component('mail::button', ['url' => $url])
Join Now
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

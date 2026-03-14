@component('mail::message')
# {{ config('app.name') }}

Hi {{ $username }},

You recently requested to reset your password. In order for us to let you proceed let us verify your credentials first
by entering this code to verification form: <br> <br>

Verification Code: <strong>{{ $code }}</strong>

Thanks,<br>
{{ config('app.name') }}
@endcomponent

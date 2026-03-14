@component('mail::message')
# {{ config('app.name') }}

Hi {{ $username }},

You have succesfully registered to our system.
In order to sign in and start using your account we need to verify that your email is valid. <br> <br>

Verification Code: <strong>{{ $code }}</strong>

Thanks,<br>
{{ config('app.name') }}
@endcomponent

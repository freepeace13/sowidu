@component('mail::message')
# {{ config('app.name') }}

Hi {{ $username }},

You have succesfully registered to our system.
In order to sign in and start using your account you need to activate it first. <br> <br>

@component('mail::button', ['url' => $redirect_url])
Activate Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

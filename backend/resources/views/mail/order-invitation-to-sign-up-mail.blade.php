@component('mail::message')

Hi {{ $client->name }}!

You've received an order from <b>{{ $company->name }}</b>!

Order details:
@component('mail::table')
| Schedule | Date |
| ------------- | ------------- |
| Order Date | {{ $order->order_date }} |
| Planned Start Date | {{ $order->planned_start_date }} |
| Planned Finish Date | {{ $order->planned_start_date }} |
@endcomponent
@component('mail::panel')
{{ $order->description }}
@endcomponent

We would like to invite you to keep track of the order together via the <a>{{ config('app.name') . ' app' }}</a>. If you want to join us, please click on the button below:

@component('mail::button', ['url' => route('auth.register', ['metadata' => $addressbookCrypt ]) ])
Sign Up Now
@endcomponent

Enjoy!

Best,<br>
{{ config('app.name') }} team
@endcomponent

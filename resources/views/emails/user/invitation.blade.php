@component('mail::message')
HI,
Click below button to complete your registration with {{ config('app.name') }}

@component('mail::button', ['url' => route('user.register')])
Register Now
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

@component('mail::message')
Account Verification
Email verificatiin Code is {{$userData->verification_code}}



Thanks,<br>
{{ config('app.name') }}
@endcomponent

{{--@formatter:off--}}
@component('mail::message')

# {{ config('app.name') }} Account Created

Dear {{$user->name}}, an account has been created for you on the {{ config('app.name') }} Public Witnessing website.

If you feel this is an error, please [contact us immediately](mailto:{{ config('mail.support.address') }}?subject=Mistaken%20User%20Account). Otherwise, please use the verification link below.

@component('mail::button', ['url' => config('app.url') . '/set-password/' . $user->id . '/' . $hashedEmail])
    Confirm Account
@endcomponent
Thank you,<br>
The {{ config('app.name') }} Team
@endcomponent

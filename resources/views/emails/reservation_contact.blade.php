<x-mail::message>
# {{ $messageSubject }}

{!! nl2br(e($messageBody)) !!}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

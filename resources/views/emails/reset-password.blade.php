@component('mail::message')
# Restablecimiento de Contraseña

Estimado/(a) {{ $user->name }} {{ $user->last_name }},<br>
Hemos recibido una solicitud para restablecer la contraseña de tu cuenta.

Tu contraseña ha sido restablecida. Aquí está tu nueva contraseña:

@component('mail::panel')
{{ $newPassword }}
@endcomponent

Con esta contraseña podras acceder a tu cuenta nuevamente.

Gracias,<br>
{{ config('app.name') }}
@endcomponent

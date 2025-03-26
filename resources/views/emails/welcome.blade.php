@component('mail::message')
# Bienvenido a Nuestro Sistema

Hola {{ $userData['name'] }},

Tu cuenta ha sido creada con éxito. Aquí están tus credenciales:

**Email:** {{ $userData['email'] }}
**Password:** {{ $userData['password'] }}

Con esta información, puede iniciar sesión en nuestro sistema.

Gracias,<br>
{{ config('app.name') }}
@endcomponent

<x-mail::message>
    # Olá, {{ $userName }}!

    Uma nova consulta foi agendada para você.

    **Consulta:** {{ $title }}

    Agradecemos por usar nosso sistema.

    Obrigado,<br>
    {{ config('app.name') }}
</x-mail::message>

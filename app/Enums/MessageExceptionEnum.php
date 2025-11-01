<?php

namespace App\Enums;
enum MessageExceptionEnum: string
{
    case APPOINTMENT_NOT_FOUND = "Consulta não encontrada.";
    case USER_HAS_APPOINTMENT_IN_THIS_TIME = "Este usuário já possui consulta marcada para este horário.";
    case USER_NOT_HAVE_APPOINTMENT = "Não existe consultas para este usuário.";
}

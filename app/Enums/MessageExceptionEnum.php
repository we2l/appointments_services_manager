<?php

namespace App\Enums;
enum MessageExceptionEnum: string
{
    case APPOINTMENT_NOT_FOUND = "Consulta não encontrada.";
    case USER_HAS_APPOINTMENT_IN_THIS_TIME = "Este usuário já possui consulta marcada para este horário.";
    case USER_NOT_HAVE_APPOINTMENT = "Não existe consultas para este usuário.";
    case INVALID_CREDENTIAL = "Credenciais inválidas.";
    case SERVICE_NOT_FOUND = "Serviço não encontrado.";
    case SERVICE_IN_USE = "Não é possível excluir este serviço, pois ele está vinculado a um ou mais agendamentos.";
}

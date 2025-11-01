<?php

namespace App\Enums;

enum AppointmentsStatusEnum: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
}

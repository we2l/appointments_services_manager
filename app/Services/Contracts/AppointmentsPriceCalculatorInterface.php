<?php

namespace App\Services\Contracts;

interface AppointmentsPriceCalculatorInterface
{
    public function calculate(array $idsService): float;
}

<?php

namespace App\Services;

use App\Repositories\Contracts\ServicesRepositoryInterface;
use App\Services\Contracts\AppointmentsPriceCalculatorInterface;

class AppointmentsPriceCalculator implements AppointmentsPriceCalculatorInterface
{
    public function __construct(
        private ServicesRepositoryInterface $servicesRepository
    )
    {}
    public function calculate(array $idsService): float
    {
        if(empty($idsService)){
            return 0.00;
        }

        return $this->servicesRepository->getTotalPriceServicesByIds($idsService);
    }
}

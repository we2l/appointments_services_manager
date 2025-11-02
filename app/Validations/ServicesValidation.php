<?php

namespace App\Validations;

use App\Enums\MessageExceptionEnum;
use App\Exceptions\ServicesException;
use App\Repositories\Contracts\ServicesRepositoryInterface;

readonly class ServicesValidation
{
    public function __construct(
        private ServicesRepositoryInterface $servicesRepository
    )
    {}

    /**
     * @param int $idService
     * @return void
     * @throws ServicesException
     */
    public function validateIfServiceCantBeDeleted(int $idService): void
    {
        $service = $this->servicesRepository->getServiceById($idService);
        if(!$service) {
            throw new ServicesException(MessageExceptionEnum::SERVICE_NOT_FOUND->value);
        }

        $isInUse = $this->servicesRepository->isServiceInUse($idService);
        if($isInUse) {
            throw new ServicesException(MessageExceptionEnum::SERVICE_IN_USE->value);
        }
    }

    /**
     * @param int $idService
     * @return void
     * @throws ServicesException
     */
    public function ensureServiceExists(int $idService): void
    {
        $service = $this->servicesRepository->getServiceById($idService);
        if(!$service) {
            throw new ServicesException(MessageExceptionEnum::SERVICE_NOT_FOUND->value);
        }
    }
}

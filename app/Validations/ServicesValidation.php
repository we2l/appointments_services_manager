<?php

namespace App\Validations;

use App\Enums\MessageExceptionEnum;
use App\Exceptions\ServiceInUseException;
use App\Exceptions\ServiceNotFoundException;
use App\Models\Service;
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
     * @throws ServiceNotFoundException
     * @throws ServiceInUseException
     */
    public function validateIfServiceCantBeDeleted(int $idService): void
    {
        $this->ensureServiceExists($idService);

        $isInUse = $this->servicesRepository->isServiceInUse($idService);

        if($isInUse) {
            throw new ServiceInUseException(MessageExceptionEnum::SERVICE_IN_USE->value);
        }
    }

    /**
     * @param int $idService
     * @return Service
     * @throws ServiceNotFoundException
     */
    public function ensureServiceExists(int $idService): Service
    {
        $service = $this->servicesRepository->getServiceById($idService);
        if(!$service) {
            throw new ServiceNotFoundException(MessageExceptionEnum::SERVICE_NOT_FOUND->value);
        }

        return $service;
    }
}

<?php

namespace App\Services;

use App\Exceptions\ServiceInUseException;
use App\Exceptions\ServiceNotFoundException;
use App\Models\Service;
use App\Repositories\Contracts\ServicesRepositoryInterface;
use App\Services\Contracts\ServicesServiceInterface;
use App\Validations\ServicesValidation;
use Illuminate\Contracts\Pagination\Paginator;

readonly class ServicesService implements ServicesServiceInterface
{
    public function __construct(
        private ServicesRepositoryInterface $servicesRepository,
        private ServicesValidation $servicesValidation
    )
    {}

    public function listAllServices(array $data): Paginator
    {
        return $this->servicesRepository->getAllServices($data);
    }

    /**
     * @param int $idService
     * @return ?Service
     * @throws ServiceNotFoundException
     */
    public function listServiceById(int $idService): ?Service
    {
        $this->servicesValidation->ensureServiceExists($idService);
        return $this->servicesRepository->getServiceById($idService);
    }

    public function createService(array $data): Service
    {
        return $this->servicesRepository->createService($data);
    }

    /**
     * @param int $idService
     * @throws ServiceNotFoundException
    */
    public function updateService(array $data, int $idService): Service
    {
        $this->servicesValidation->ensureServiceExists($idService);
        return $this->servicesRepository->updateServiceById($data, $idService);
    }

    /**
     * @param int $idService
     * @return void
     * @throws ServiceNotFoundException
     * @throws ServiceInUseException
    */
    public function deleteService(int $idService): void
    {
        $this->servicesValidation->validateIfServiceCantBeDeleted($idService);
        $this->servicesRepository->deleteServiceById($idService);
    }
}

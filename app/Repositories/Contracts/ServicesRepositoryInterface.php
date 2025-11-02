<?php

namespace App\Repositories\Contracts;

use App\Models\Service;
use Illuminate\Contracts\Pagination\Paginator;

interface ServicesRepositoryInterface
{
    public function getAllServices(array $data): Paginator;
    public function getServiceById(int $id): ?Service;
    public function createService(array $data): Service;
    public function updateServiceById(array $data, int $idService): Service;
    public function deleteServiceById(int $idService): void;
    public function isServiceInUse(int $idService): bool;
    public function getTotalPriceServicesByIds(array $ids): float;
}

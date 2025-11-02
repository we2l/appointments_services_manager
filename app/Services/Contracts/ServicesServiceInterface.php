<?php

namespace App\Services\Contracts;

use App\Models\Service;
use Illuminate\Contracts\Pagination\Paginator;

interface ServicesServiceInterface
{
    public function listAllServices(array $data): Paginator;
    public function listServiceById(int $idService): ?Service;
    public function createService(array $data): Service;
    public function updateService(array $data, int $idService): Service;
    public function deleteService(int $idService): void;
}

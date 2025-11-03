<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Contracts\ServicesRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;

class ServicesRepository implements ServicesRepositoryInterface
{
    public function getAllServices(array $data): Paginator
    {
        $perPage = $data['per_page'] ?? 10;
        return Service::select(
            'id',
            'name',
            'price'
        )->simplePaginate($perPage);
    }

    public function getServiceById(int $id): ?Service
    {
        return Service::where('id', $id)->first();
    }

    public function createService(array $data): Service
    {
        return Service::create([
            'name' => $data['name'],
            'price' => $data['price']
        ]);
    }

    public function updateServiceById(array $data, int $idService): Service
    {
        $service = $this->getServiceById($idService);
        $service->update($data);

        return $service;
    }

    public function deleteServiceById(int $idService): void
    {
        Service::where('id', $idService)->delete();
    }

    public function isServiceInUse(int $idService): bool
    {
        return Service::where('id', $idService)->has('appointments')->exists();
    }

    public function getTotalPriceServicesByIds(array $ids): float
    {
        return Service::whereIn('id', $ids)
            ->sum('price');
    }
}

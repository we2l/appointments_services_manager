<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServicesRequest;
use App\Http\Requests\UpdateServicesRequest;
use App\Http\Resources\ServicesResource;
use App\Services\Contracts\ServicesServiceInterface;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function __construct(
        private ServicesServiceInterface $servicesService
    )
    {}

    public function index(Request $request)
    {
        return ServicesResource::collection($this->servicesService->listAllServices($request->all()));
    }

    public function show(int $id)
    {
        return response()->json(
            new ServicesResource($this->servicesService->listServiceById($id))
        );
    }

    public function store(StoreServicesRequest $request)
    {
        return response()->json(
            new ServicesResource($this->servicesService->createService($request->all())),
            201
        );
    }

    public function update(UpdateServicesRequest $request, int $idService)
    {
        return response()->json(
            new ServicesResource($this->servicesService->updateService($request->all(), $idService))
        );
    }

    public function delete(int $id)
    {
        $this->servicesService->deleteService($id);
        return response()->noContent();
    }
}

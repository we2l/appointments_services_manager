<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexAppointmentsRequest;
use App\Http\Requests\StoreAppointmentsRequest;
use App\Http\Requests\UpdateAppointmentsRequest;
use App\Http\Resources\AppointmentsResource;
use App\Services\Contracts\AppointmentsServiceInterface;

class AppointmentsController extends Controller
{
    public function __construct(
        private AppointmentsServiceInterface $appointmentsService
    )
    {}

    public function index(IndexAppointmentsRequest $request)
    {
        return AppointmentsResource::collection($this->appointmentsService->listAllAppointments($request->all()));
    }

    public function show(int $idAppointment)
    {
        return response()->json(
            new AppointmentsResource($this->appointmentsService->listAppointmentById($idAppointment))
        );
    }

    public function store(StoreAppointmentsRequest $request)
    {
        return response()->json(
            new AppointmentsResource($this->appointmentsService->createAppointment($request->all())),
            201
        );
    }

    public function update(UpdateAppointmentsRequest $request, int $idAppointment)
    {
        return response()->json([
            new AppointmentsResource($this->appointmentsService->updateAppointment($request->all(), $idAppointment))
        ]);
    }

    public function cancel(int $idAppointment)
    {
        $this->appointmentsService->cancelAppointment($idAppointment);
        return response()->noContent();
    }

}

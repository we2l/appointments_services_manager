<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelAppointmentsRequest;
use App\Http\Requests\IndexAppointmentsRequest;
use App\Http\Requests\ShowAppointmentsRequest;
use App\Http\Requests\StoreAppointmentsRequest;
use App\Http\Requests\UpdateAppointmentsRequest;
use App\Http\Resources\AppointmentsResource;
use App\Models\Appointments;
use App\Services\Contracts\AppointmentsServiceInterface;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
            new AppointmentsResource($this->appointmentsService->createAppointment($request->all()))
        );
    }

    public function update(UpdateAppointmentsRequest $request, int $idAppointment)
    {
        $this->appointmentsService->updateAppointment($request->all(), $idAppointment);
        return response()->noContent();
    }

    public function cancel(int $idAppointment)
    {
        $this->appointmentsService->cancelAppointment($idAppointment);
        return response()->noContent();
    }

}

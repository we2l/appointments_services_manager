<?php

namespace App\Services;
use App\Exceptions\AppointmentsException;
use App\Mail\AppointmentMail;
use App\Models\Appointments;
use App\Repositories\Contracts\AppointmentsRepositoryInterface;
use App\Services\Contracts\AppointmentsServiceInterface;
use App\Validations\AppointmentsValidation;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AppointmentsService implements AppointmentsServiceInterface
{
    public function __construct(
        private AppointmentsRepositoryInterface $appointmentsRepository,
        private AppointmentsValidation $appointmentsValidation,
    )
    {}

    public function listAllAppointments(array $data): Paginator
    {
        return $this->appointmentsRepository->getAllAppointments($data);
    }

    /**
     * @param int $idAppointment
     * @return ?Appointments
     * @throws AppointmentsException
     */
    public function listAppointmentById(int $idAppointment): ?Appointments
    {
        $this->appointmentsValidation->ensureIfAppointmentExists($idAppointment);
        return $this->appointmentsRepository->getAppointmentById($idAppointment);
    }

    /**
     * @param array $data
     * @return Appointments
     * @throws AppointmentsException
     */
    public function createAppointment(array $data): Appointments
    {
        $this->appointmentsValidation->ensureAppointmentExists($data);
        $appointmentCreated = DB::transaction(function () use ($data) {
            return $this->appointmentsRepository->createAppointment($data);
        });

        if ($appointmentCreated && $appointmentCreated->user) {
            Mail::to($appointmentCreated->user->email)
                ->queue(new AppointmentMail($appointmentCreated->user->name, $appointmentCreated->title));
        }

        return $appointmentCreated;
    }

    /**
     * @param array $data
     * @param int $idAppointment
     * @return void
     * @throws AppointmentsException
     */
    public function updateAppointment(array $data, int $idAppointment): void
    {
        $this->appointmentsValidation->ensureIfAppointmentExists($idAppointment);
        $this->appointmentsRepository->updateAppointment($data, $idAppointment);
    }

    public function cancelAppointment(int $idAppointment): void
    {
        $this->appointmentsRepository->cancelStatusAppointment($idAppointment);
    }
}

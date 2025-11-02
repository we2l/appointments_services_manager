<?php

namespace App\Services;
use App\Exceptions\AppointmentsException;
use App\Mail\AppointmentMail;
use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentsRepositoryInterface;
use App\Services\Contracts\AppointmentsPriceCalculatorInterface;
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
        private AppointmentsPriceCalculatorInterface $appointmentsPriceCalculator,
    )
    {}

    public function listAllAppointments(array $data): Paginator
    {
        return $this->appointmentsRepository->getAllAppointments($data);
    }

    /**
     * @param int $idAppointment
     * @return ?Appointment
     * @throws AppointmentsException
     */
    public function listAppointmentById(int $idAppointment): ?Appointment
    {
        return $this->appointmentsValidation->ensureIfAppointmentExists($idAppointment);
    }

    /**
     * @param array $data
     * @return Appointment
     * @throws AppointmentsException
     */
    public function createAppointment(array $data): Appointment
    {
        $totalPrice = $this->appointmentsPriceCalculator->calculate($data['services'] ?? []);
        $data['total_price'] = $totalPrice;

        $this->appointmentsValidation->ensureAppointmentExists($data);

        $appointmentCreated = DB::transaction(function () use ($data) {
            $appointment = $this->appointmentsRepository->createAppointment($data);

            $this->appointmentsRepository->syncServices($appointment, $data['services']);

            return $appointment;
        });

        if ($appointmentCreated && $appointmentCreated->user) {
            Mail::to($appointmentCreated->user->email)
                ->queue(new AppointmentMail($appointmentCreated->user->name, $appointmentCreated->title));
        }

        return $this->appointmentsRepository->loadRelations($appointmentCreated);
    }

    /**
     * @param array $data
     * @param int $idAppointment
     * @return void
     * @throws AppointmentsException
     */
    public function updateAppointment(array $data, int $idAppointment): Appointment
    {
        $appointment = $this->appointmentsValidation->ensureIfAppointmentExists($idAppointment);

        $totalPrice = $this->appointmentsPriceCalculator->calculate($data['services'] ?? []);
        $data['total_price'] = $totalPrice;

        $appointmentUpdated = DB::transaction(function () use ($data, $idAppointment, $appointment) {

            $appointmentUpdate = $this->appointmentsRepository->updateAppointment($data, $idAppointment);
            if (array_key_exists('services', $data)) {
                $this->appointmentsRepository->syncServices($appointment, $data['services']);
            }

            return $appointmentUpdate;
        });

        return $this->appointmentsRepository->loadRelations($appointmentUpdated);
    }

    public function cancelAppointment(int $idAppointment): void
    {
        $this->appointmentsRepository->cancelStatusAppointment($idAppointment);
    }
}

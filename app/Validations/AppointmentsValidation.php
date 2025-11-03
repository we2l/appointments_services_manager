<?php

namespace App\Validations;

use App\Enums\MessageExceptionEnum;
use App\Exceptions\AppointmentsException;
use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentsRepositoryInterface;
use Carbon\Carbon;

readonly class AppointmentsValidation
{
    public function __construct(
        private AppointmentsRepositoryInterface $appointmentsRepository
    )
    {}

    /**
     * @param int $idAppointment
     * @return void
     * @throws AppointmentsException
     */
    public function ensureAppointmentExists(int $idAppointment): Appointment
    {
        $appointment = $this->appointmentsRepository->getAppointmentById($idAppointment);
        if(!$appointment) {
            throw new AppointmentsException(MessageExceptionEnum::APPOINTMENT_NOT_FOUND->value);
        }

        return $appointment;
    }

    /**
     * @param array $data
     * @return void
     * @throws AppointmentsException
     */
    public function ensureUserCanBookAt(array $data) : void
    {
        $hasConflitTime = $this->appointmentsRepository->userHasAppointmentAt($data['user_id'], $data['scheduled_at']);
        if($hasConflitTime) {
            throw new AppointmentsException(MessageExceptionEnum::USER_HAS_APPOINTMENT_IN_THIS_TIME->value);
        }
    }
}

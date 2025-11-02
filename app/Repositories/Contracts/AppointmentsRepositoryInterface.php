<?php

namespace App\Repositories\Contracts;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentsRepositoryInterface
{
    public function createAppointment(array $data): Appointment;
    public function getAppointmentsByUserId(int $userId): Collection;
    public function getAppointmentById(int $id): ?Appointment;
    public function updateAppointment(array $data, int $idAppointment): Appointment;
    public function cancelStatusAppointment(int $idAppointment): void;
    public function userHasAppointmentAt(int $userId, string $scheduledAt): bool;
    public function syncServices(Appointment $appointment, array $servicesId): void;
    public function loadRelations(Appointment $appointment): Appointment;
}

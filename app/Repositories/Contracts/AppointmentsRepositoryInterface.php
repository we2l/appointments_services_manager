<?php

namespace App\Repositories\Contracts;

use App\Models\Appointments;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentsRepositoryInterface
{
    public function createAppointment(array $data): Appointments;
    public function getAppointmentsByUserId(int $userId): Collection;
    public function getAppointmentById(int $id): ?Appointments;
    public function updateAppointment(array $data, int $idAppointment): void;
    public function cancelStatusAppointment(int $idAppointment): void;
    public function userHasAppointmentAt(int $userId, string $scheduledAt): bool;
}

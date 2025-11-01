<?php

namespace App\Services\Contracts;

interface AppointmentsServiceInterface
{
    public function listAllAppointments(array $data);
    public function listAppointmentById(int $idAppointment);
    public function createAppointment(array $data);
    public function updateAppointment(array $data, int $idAppointment);
    public function cancelAppointment(int $idAppointment);
}

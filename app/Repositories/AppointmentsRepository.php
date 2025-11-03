<?php

namespace App\Repositories;

use App\Enums\AppointmentsStatusEnum;
use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentsRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
class AppointmentsRepository implements AppointmentsRepositoryInterface
{
    public function getAllAppointments(array $data): Paginator
    {
        $cacheKey = 'appointments:' . md5(http_build_query($data));
        $cacheTTL = now()->addMinutes(10);
        $perPage = $data['per_page'] ?? 10;
        return Cache::tags(['appointments'])->remember($cacheKey, $cacheTTL, function () use ($perPage) {
            \Log::info('Utilizando Redis... ');

            return Appointment::with('user', 'services')->select(
                'id',
                'user_id',
                'title',
                'total_price',
                'scheduled_at',
                'status',
            )->simplePaginate($perPage);
        });
    }
    public function createAppointment(array $data): Appointment
    {
        Cache::tags(['appointments'])->flush();
        $appointment = Appointment::create([
            'title'         => $data['title'],
            'scheduled_at'  => $data['scheduled_at'],
            'status'        => $data['status'],
            'user_id'       => $data['user_id'],
            'total_price'   => $data['total_price'],
        ]);

        $appointment->load('user');

        return $appointment;
    }

    public function getAppointmentsByUserId(int $userId): Collection
    {
        return Appointment::where('user_id', $userId)->get();
    }

    public function getAppointmentById(int $id): ?Appointment
    {
        return Appointment::with('user', 'services')->find($id);
    }

    public function userHasAppointmentAt(int $userId, string $scheduledAt): bool
    {
        $schedule = Carbon::parse($scheduledAt);
        return Appointment::where('user_id', $userId)
            ->where('scheduled_at', $schedule)
            ->exists();
    }

    public function updateAppointment(array $data, int $idAppointment): Appointment
    {
        Cache::tags(['appointments'])->flush();
        $appointment = $this->getAppointmentById($idAppointment);
        $appointment->update($data);
        return $appointment;
    }

    public function cancelStatusAppointment(int $idAppointment): void
    {
        Cache::tags(['appointments'])->flush();
        Appointment::where('id', $idAppointment)
            ->update([
                'status' => AppointmentsStatusEnum::CANCELLED,
            ]);
    }

    public function syncServices(Appointment $appointment, array $servicesId): void
    {
        if(!empty($servicesId)){
            $appointment->services()->sync($servicesId);
        }
    }

    public function loadRelations(Appointment $appointment): Appointment
    {
        return $appointment->load('services', 'user');
    }
}

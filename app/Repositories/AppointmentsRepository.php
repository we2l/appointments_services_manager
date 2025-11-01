<?php

namespace App\Repositories;

use App\Enums\AppointmentsStatusEnum;
use App\Models\Appointments;
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
            return Appointments::select(
                'id',
                'user_id',
                'title',
                'scheduled_at',
                'status',
            )->simplePaginate($perPage);
        });
    }
    public function createAppointment(array $data): Appointments
    {
        Cache::tags(['appointments'])->flush();
        $appointment = Appointments::create([
            'title' => $data['title'],
            'scheduled_at' => $data['scheduled_at'],
            'status' => $data['status'],
            'user_id' => $data['user_id']
        ]);

        $appointment->load('user');

        return $appointment;
    }

    public function getAppointmentsByUserId(int $userId): Collection
    {
        return Appointments::where('user_id', $userId)->get();
    }

    public function getAppointmentById(int $id): ?Appointments
    {
        return Appointments::find($id);
    }

    public function userHasAppointmentAt(int $userId, string $scheduledAt): bool
    {
        $schedule = Carbon::parse($scheduledAt);
        return Appointments::where('user_id', $userId)
            ->where('scheduled_at', $schedule)
            ->exists();
    }

    public function updateAppointment(array $data, int $idAppointment): void
    {
        Cache::tags(['appointments'])->flush();
        Appointments::where('id', $idAppointment)
            ->update([
               'title' => $data['title'],
               'scheduled_at' => $data['scheduled_at'],
               'status' => $data['status'],
               'user_id' => $data['user_id'],
            ]);
    }

    public function cancelStatusAppointment(int $idAppointment): void
    {
        Cache::tags(['appointments'])->flush();
        Appointments::where('id', $idAppointment)
            ->update([
                'status' => AppointmentsStatusEnum::CANCELLED,
            ]);
    }
}

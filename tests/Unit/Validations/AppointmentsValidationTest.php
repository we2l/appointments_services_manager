<?php

namespace Tests\Unit\Validations;

use App\Exceptions\AppointmentsException;
use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentsRepositoryInterface;
use App\Validations\AppointmentsValidation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AppointmentsValidationTest extends TestCase
{
    private MockObject|AppointmentsRepositoryInterface $repositoryMock;
    private AppointmentsValidation $validation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->createMock(AppointmentsRepositoryInterface::class);
        $this->validation = new AppointmentsValidation($this->repositoryMock);
    }

    public function test_ensure_appointment_exists_returns_model_when_found(): void
    {
        $mockAppointment = $this->createMock(Appointment::class);
        $appointmentId = 1;

        $this->repositoryMock
            ->expects($this->once())
            ->method('getAppointmentById')
            ->with($appointmentId)
            ->willReturn($mockAppointment);

        $result = $this->validation->ensureAppointmentExists($appointmentId);

        $this->assertSame($mockAppointment, $result);
    }

    public function test_ensure_appointment_exists_throws_exception_when_not_found(): void
    {
        $appointmentId = 999;

        $this->repositoryMock
            ->expects($this->once())
            ->method('getAppointmentById')
            ->with($appointmentId)
            ->willReturn(null);

        $this->expectException(AppointmentsException::class);

        $this->validation->ensureAppointmentExists($appointmentId);
    }

    public function test_ensure_user_can_book_at_passes_when_no_conflict_exists(): void
    {
        $data = [
            'user_id' => 1,
            'scheduled_at' => '2025-10-10 10:00:00'
        ];

        $this->repositoryMock
            ->expects($this->once())
            ->method('userHasAppointmentAt')
            ->with(1, '2025-10-10 10:00:00')
            ->willReturn(false);

        $this->validation->ensureUserCanBookAt($data);

        $this->assertTrue(true);
    }

    public function test_ensure_user_can_book_at_throws_exception_when_conflict_exists(): void
    {
        $data = [
            'user_id' => 1,
            'scheduled_at' => '2025-10-10 10:00:00'
        ];

        $this->repositoryMock
            ->expects($this->once())
            ->method('userHasAppointmentAt')
            ->with(1, '2025-10-10 10:00:00')
            ->willReturn(true);

        $this->expectException(AppointmentsException::class);

        $this->validation->ensureUserCanBookAt($data);
    }
}

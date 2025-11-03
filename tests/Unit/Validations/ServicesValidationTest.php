<?php

namespace Tests\Unit\Validations;

use App\Exceptions\ServiceInUseException;
use App\Exceptions\ServiceNotFoundException;
use App\Models\Service;
use App\Repositories\Contracts\ServicesRepositoryInterface;
use App\Validations\ServicesValidation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ServicesValidationTest extends TestCase
{
    private MockObject|ServicesRepositoryInterface $repositoryMock;
    private ServicesValidation $validation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->createMock(ServicesRepositoryInterface::class);
        $this->validation = new ServicesValidation($this->repositoryMock);
    }

    public function test_ensure_service_exists_returns_service_if_found(): void
    {
        $mockService = $this->createMock(Service::class);
        $serviceId = 1;

        $this->repositoryMock
            ->expects($this->once())
            ->method('getServiceById')
            ->with($serviceId)
            ->willReturn($mockService);

        $result = $this->validation->ensureServiceExists($serviceId);

        $this->assertSame($mockService, $result);
    }

    public function test_ensure_service_exists_throws_exception_if_not_found(): void
    {
        $serviceId = 999;

        $this->repositoryMock
            ->expects($this->once())
            ->method('getServiceById')
            ->with($serviceId)
            ->willReturn(null);

        $this->expectException(ServiceNotFoundException::class);

        $this->validation->ensureServiceExists($serviceId);
    }

    public function test_validate_if_service_cant_be_deleted_passes_if_not_in_use(): void
    {
        $serviceId = 1;
        $mockService = $this->createMock(Service::class);

        $this->repositoryMock
            ->expects($this->once())
            ->method('getServiceById')
            ->with($serviceId)
            ->willReturn($mockService);

        $this->repositoryMock
            ->expects($this->once())
            ->method('isServiceInUse')
            ->with($serviceId)
            ->willReturn(false);

        $this->validation->validateIfServiceCantBeDeleted($serviceId);

        $this->assertTrue(true);
    }

    public function test_validate_if_service_cant_be_deleted_throws_exception_if_in_use(): void
    {
        $serviceId = 1;
        $mockService = $this->createMock(Service::class);

        $this->repositoryMock
            ->expects($this->once())
            ->method('getServiceById')
            ->with($serviceId)
            ->willReturn($mockService);

        $this->repositoryMock
            ->expects($this->once())
            ->method('isServiceInUse')
            ->with($serviceId)
            ->willReturn(true);

        $this->expectException(ServiceInUseException::class);

        // ACT
        $this->validation->validateIfServiceCantBeDeleted($serviceId);
    }
}

<?php

namespace Tests\Unit\Services;

use App\Repositories\Contracts\ServicesRepositoryInterface;
use App\Services\AppointmentsPriceCalculator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AppointmentPriceCalculatorTest extends TestCase
{
    private MockObject|ServicesRepositoryInterface $repositoryMock;
    private AppointmentsPriceCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->createMock(ServicesRepositoryInterface::class);
        $this->calculator = new AppointmentsPriceCalculator($this->repositoryMock);
    }

    public function test_it_calculates_the_sum_of_multiple_services(): void
    {
        $serviceIds = [1, 5, 10];
        $expectedTotal = 275.50;

        $this->repositoryMock
            ->expects($this->once())
            ->method('getTotalPriceServicesByIds')
            ->with($serviceIds)
            ->willReturn($expectedTotal);

        $total = $this->calculator->calculate($serviceIds);

        $this->assertEquals($expectedTotal, $total);
    }

    public function test_it_returns_zero_for_an_empty_array_of_services(): void
    {
        $serviceIds = [];
        $expectedTotal = 0.00;

        $this->repositoryMock
            ->expects($this->never())
            ->method('getTotalPriceServicesByIds')
            ->with($serviceIds)
            ->willReturn($expectedTotal);

        $total = $this->calculator->calculate($serviceIds);

        $this->assertEquals($expectedTotal, $total);
    }
}

<?php

namespace App\Providers;

use App\Repositories\AppointmentsRepository;
use App\Repositories\AuthRepository;
use App\Repositories\Contracts\AppointmentsRepositoryInterface;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Contracts\ServicesRepositoryInterface;
use App\Repositories\ServicesRepository;
use App\Services\AppointmentsPriceCalculator;
use App\Services\AppointmentsService;
use App\Services\AuthService;
use App\Services\Contracts\AppointmentsPriceCalculatorInterface;
use App\Services\Contracts\AppointmentsServiceInterface;
use App\Services\Contracts\AuthServiceInterface;
use App\Services\Contracts\ServicesServiceInterface;
use App\Services\ServicesService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AppointmentsServiceInterface::class, AppointmentsService::class);
        $this->app->bind(AppointmentsRepositoryInterface::class, AppointmentsRepository::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(ServicesServiceInterface::class, ServicesService::class);
        $this->app->bind(ServicesRepositoryInterface::class, ServicesRepository::class);
        $this->app->bind(AppointmentsPriceCalculatorInterface::class, AppointmentsPriceCalculator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

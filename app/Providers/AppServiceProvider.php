<?php

namespace App\Providers;

use App\Repositories\AppointmentsRepository;
use App\Repositories\Contracts\AppointmentsRepositoryInterface;
use App\Services\AppointmentsService;
use App\Services\Contracts\AppointmentsServiceInterface;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

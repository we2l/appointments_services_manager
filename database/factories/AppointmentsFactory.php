<?php

namespace Database\Factories;

use App\Enums\AppointmentsStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'status' => AppointmentsStatusEnum::PENDING,
            'scheduled_at' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'user_id' => User::factory(),
        ];
    }
}

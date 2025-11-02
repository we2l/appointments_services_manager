<?php

namespace Tests\Feature\Appointments;

use App\Mail\AppointmentMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Appointment;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Mail;

class AppointmentsTest extends TestCase
{
    use RefreshDatabase;

    private User $userMock;
    private string $baseUrl = '/api/appointments';

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_create_an_appointment(): void
    {
        Queue::fake();
        Mail::fake();

        $appointmentData = [
            'title' => 'Consulta de Rotina',
            'scheduled_at' => now()->addDay()->toDateTimeString(),
            'status' => 'pending',
            'user_id' => $this->user->id
        ];

        $response = $this->postJson($this->baseUrl, $appointmentData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('appointments', [
            'title' => 'Consulta de Rotina',
            'user_id' => $this->user->id
        ]);

        $response->assertJsonFragment(['title' => 'Consulta de Rotina']);
        Mail::assertQueued(AppointmentMail::class);
    }

    public function test_can_list_appointments(): void
    {
        Appointment::factory()->create(['title' => 'Agendamento A', 'user_id' => $this->user->id]);
        Appointment::factory()->create(['title' => 'Agendamento B', 'user_id' => $this->user->id]);

        $response = $this->getJson($this->baseUrl . '?per_page=5');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'scheduled_at', 'status']
            ],
            'links',
            'meta'
        ]);

        $response->assertJsonFragment(['title' => 'Agendamento A']);
    }
}

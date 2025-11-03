<?php

namespace Tests\Feature\Appointments;

use App\Mail\AppointmentMail;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $serviceA = Service::factory()->create(['price' => 100.00]);
        $serviceB = Service::factory()->create(['price' => 50.50]);

        $appointmentData = [
            'title'         => 'Consulta de Rotina',
            'scheduled_at'  => now()->addDay()->toDateTimeString(),
            'status'        => 'pending',
            'user_id'       => $this->user->id,
            'services'      => [$serviceA->id, $serviceB->id],
        ];

        $response = $this->postJson($this->baseUrl, $appointmentData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('appointments', [
            'title'         => 'Consulta de Rotina',
            'user_id'       => $this->user->id,
            'total_price'   => 150.50
        ]);

        $appointment = Appointment::first();
        $this->assertDatabaseHas('appointment_service', [
            'appointment_id' => $appointment->id,
            'service_id'     => $serviceA->id,
        ]);
        $this->assertDatabaseHas('appointment_service', [
            'appointment_id' => $appointment->id,
            'service_id'     => $serviceB->id,
        ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'total_price',
                'user' => ['id', 'name'],
                'services' => [
                    '*' => ['id', 'name', 'price']
                ]
            ]
        ]);

        $response->assertJsonFragment(['title' => 'Consulta de Rotina']);
        Mail::assertQueued(AppointmentMail::class);
    }

    public function test_it_can_show_a_single_appointment(): void
    {
        $service = Service::factory()->create(['name' => 'Serviço de Limpeza']);

        $appointment = Appointment::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Agendamento de Teste'
        ]);

        $appointment->services()->attach($service->id);
        $response = $this->getJson($this->baseUrl . '/' . $appointment->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'total_price',
                'user' => ['id', 'name', 'email'],
                'services' => [
                    '*' => ['id', 'name', 'price']
                ]
            ]
        ]);
        $response->assertJsonFragment([
            'title' => 'Agendamento de Teste',
            'name' => 'Serviço de Limpeza'
        ]);
    }

    public function test_it_returns_not_found_when_showing_an_invalid_appointment(): void
    {
        $invalidId = 9999;
        $response = $this->getJson($this->baseUrl . '/' . $invalidId);
        $response->assertStatus(404);
    }

    public function test_can_list_appointments(): void
    {
        $service = Service::factory()->create(['name' => 'Serviço de Teste']);
        $appointment = Appointment::factory()->create([
            'title' => 'Agendamento A',
            'user_id' => $this->user->id
        ]);
        $appointment->services()->attach($service->id);
        Appointment::factory()->create(['title' => 'Agendamento B', 'user_id' => $this->user->id]);

        $response = $this->getJson($this->baseUrl . '?per_page=5');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'scheduled_at',
                    'status',
                    'total_price',
                    'user_id',
                    'user' => ['id', 'name', 'email'],
                    'services' => [
                        '*' => ['id', 'name', 'price']
                    ]
                ]
            ],
            'links',
            'meta'
        ]);

        $response->assertJsonFragment(['title' => 'Agendamento A']);

        $response->assertJsonFragment(['name' => 'Serviço de Teste']);
    }

    public function test_it_can_update_an_appointment(): void
    {
        $serviceA = Service::factory()->create(['price' => 100.00]);
        $serviceB = Service::factory()->create(['price' => 30.00]);
        $serviceC = Service::factory()->create(['price' => 20.00]);

        $appointment = Appointment::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Título Antigo',
            'total_price' => 100.00
        ]);

        $appointment->services()->attach($serviceA->id);
        $updateData = [
            'title' => 'Título Atualizado',
            'services' => [$serviceB->id, $serviceC->id],
            'scheduled_at' => $appointment->scheduled_at->toDateTimeString(),
            'status' => $appointment->status,
            'user_id' => $appointment->user_id
        ];

        $response = $this->putJson($this->baseUrl . '/' . $appointment->id, $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'title' => 'Título Atualizado',
            'total_price' => 50.00
        ]);

        $this->assertDatabaseMissing('appointment_service', [
            'appointment_id' => $appointment->id,
            'service_id' => $serviceA->id
        ]);

        $this->assertDatabaseHas('appointment_service', [
            'appointment_id' => $appointment->id,
            'service_id' => $serviceB->id
        ]);
        $this->assertDatabaseHas('appointment_service', [
            'appointment_id' => $appointment->id,
            'service_id' => $serviceC->id
        ]);

        $response->assertJsonFragment(['title' => 'Título Atualizado']);
        $response->assertJsonFragment(['name' => $serviceB->name]);
    }

    public function test_it_can_cancel_an_appointment(): void
    {
        $appointment = Appointment::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $response = $this->patchJson($this->baseUrl . '/cancel/' . $appointment->id);

        $response->assertStatus(204);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled'
        ]);

        $this->assertDatabaseMissing('appointments', [
            'id' => $appointment->id,
            'status' => 'pending'
        ]);
    }
}

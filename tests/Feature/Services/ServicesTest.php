<?php

namespace Tests\Feature\Services;

use App\Enums\MessageExceptionEnum;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ServicesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $baseUrl = '/api/services';

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_it_can_create_a_service(): void
    {
        $serviceData = [
            'name' => 'Material',
            'price' => 150.75
        ];

        $response = $this->postJson($this->baseUrl, $serviceData);
        $response->assertStatus(201);

        $this->assertDatabaseHas('services', [
            'name' => 'Material',
            'price' => 150.75
        ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'price'
            ]
        ]);

        $response->assertJsonFragment(['name' => 'Material']);
    }

    public function test_it_can_list_services(): void
    {
        Service::factory()->create(['name' => 'Serviço A']);
        Service::factory()->create(['name' => 'Serviço B']);
        Service::factory()->create(['name' => 'Serviço C']);

        $response = $this->getJson($this->baseUrl . '?per_page=2');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'price']
            ],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => [
                'current_page',
                'from',
                'path',
                'per_page',
                'to'
            ]
        ]);

        $response->assertJsonFragment(['name' => 'Serviço A']);
        $response->assertJsonCount(2, 'data');
    }

    public function test_it_can_show_a_single_service(): void
    {
        $service = Service::factory()->create([
            'name' => 'Equipamento',
            'price' => 180.00
        ]);

        $response = $this->getJson($this->baseUrl . '/' . $service->id);

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'id' => $service->id,
                'name' => 'Equipamento',
                'price' => '180.00'
            ]
        ]);
    }

    public function test_it_returns_not_found_when_showing_an_invalid_service(): void
    {
        $invalidId = 99999;
        $response = $this->getJson($this->baseUrl . '/' . $invalidId);

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'message' => MessageExceptionEnum::SERVICE_NOT_FOUND->value
        ]);
    }

    public function test_it_can_update_a_service(): void
    {
        $service = Service::factory()->create([
            'name' => 'Nome Antigo',
            'price' => 100.00
        ]);

        $updateData = [
            'name' => 'Nome Atualizado',
            'price' => 199.99
        ];

        $response = $this->putJson($this->baseUrl . '/' . $service->id, $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Nome Atualizado',
            'price' => 199.99
        ]);

        $response->assertJson([
            'data' => [
                'id' => $service->id,
                'name' => 'Nome Atualizado',
                'price' => '199.99'
            ]
        ]);
    }

    public function test_it_can_delete_a_service(): void
    {
        $service = Service::factory()->create();

        $response = $this->deleteJson($this->baseUrl . '/' . $service->id);
        $response->assertStatus(204);

        $this->assertDatabaseMissing('services', [
            'id' => $service->id
        ]);
    }

    public function test_it_prevents_deleting_a_service_that_is_in_use(): void
    {
        $service = Service::factory()->create();
        $appointment = Appointment::factory()->create([
            'user_id' => $this->user->id
        ]);

        $appointment->services()->attach($service->id);
        $response = $this->deleteJson($this->baseUrl . '/' . $service->id);

        $response->assertStatus(422);

        $this->assertDatabaseHas('services', [
            'id' => $service->id
        ]);

        $response->assertJsonFragment([
            'message' => MessageExceptionEnum::SERVICE_IN_USE->value
        ]);
    }
}

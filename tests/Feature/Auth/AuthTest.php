<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_succesfully(): void
    {
        $userData = [
          'name' => 'John Doe',
          'email' => 'john@doe.com',
          'password' => 'teste123',
        ];

        $response = $this->post('api/auth/register', $userData);
        $response->assertStatus(201);;

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
        ]);
    }

    public function test_registration_fails_if_email_already_exists(): void
    {
        User::factory()->create(['email' => 'john@doe.com']);

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'teste123'
        ];

        $response = $this->postJson('api/auth/register', $userData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_user_can_login_succesfully(): void
    {
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => Hash::make('teste123')
        ]);

        $userData = [
          'email' => 'john@doe.com',
          'password' => 'teste123'
        ];

        $response = $this->postJson('api/auth/login', $userData);
        $response->assertStatus(200);
    }
}

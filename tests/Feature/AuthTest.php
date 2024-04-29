<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function login($credentials = [])
    {
        $defaultCredentials = [
            'email' => 'test@example.com',
            'password' => '12345678',
        ];

        $response = $this->post('/api/auth/login', array_merge($defaultCredentials, $credentials));
        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true);

        return $data['data']['token'];
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('12345678'),
        ]);

        $response = $this->post('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(200);
    }

    public function test_user_can_register()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];

        $response = $this->post('/api/auth/register', $userData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_user_cannot_register_with_existing_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'name' => 'Jane Doe',
            'email' => 'existing@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];

        $response = $this->post('/api/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_cannot_register_if_password_confirmation_does_not_match()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '12345678',
            'password_confirmation' => '55555555',
        ];

        $response = $this->post('/api/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        $this->assertDatabaseMissing('users', ['email' => 'john@example.com']);
    }

    public function test_protected_route_with_token()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('12345678'),
        ]);

        $token = $this->login([
            'email' => 'test@example.com',
            'password' => '12345678',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/auth/user-profile');

        $response->assertStatus(200);
    }

    public function test_protected_route_without_token()
    {

        $response = $this->get('/api/auth/user-profile');

        $response->assertStatus(404);
    }
}

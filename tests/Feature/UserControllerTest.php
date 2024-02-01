<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    /** @test */
    public function register()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123456',
        ];

        $response = $this->json('POST', '/api/register', $userData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    /** @test */
    public function login()
    {
        $userData = [
            'email' => 'john@example.com',
            'password' => 'secret123456',
        ];

        $response = $this->json('POST', '/api/login', $userData);

        $response->assertStatus(200);
    }

    /** @test */
    public function logout()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/logout');

        $response->assertStatus(200);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'token' => hash('sha256', $token),
        ]);
    }

    /** @test */
    public function createSubscription()
    {
        $data = [
            "renewed_at" => "2024-01-01 12:00:00",
            "expired_at" => "2024-06-06 12:00:00"
        ];

        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->postJson('/api/user/' . $user->id . '/subscription', $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);
        
        $response->assertStatus(201);
    }

    
}

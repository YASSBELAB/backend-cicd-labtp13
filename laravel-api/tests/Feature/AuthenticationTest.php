<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'USER']);
        Role::firstOrCreate(['name' => 'ADMIN']);
    }

    public function test_user_can_login_and_access_protected_route(): void
    {
        $user = User::factory()->create(['password' => bcrypt('Pass1234')]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'Pass1234',
            'device_name' => 'phpunit',
        ]);

        $response->assertOk();
        $token = $response->json('access_token');

        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('email', $user->email);
    }

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'access_token',
            'token_type',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('Pass1234')]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'WrongPassword',
            'device_name' => 'phpunit',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials',
        ]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create(['password' => bcrypt('Pass1234')]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'Pass1234',
            'device_name' => 'phpunit',
        ]);

        $token = $loginResponse->json('access_token');

        auth()->logout();
        $this->flushSession();

        $logoutResponse = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/auth/logout');

        $logoutResponse->assertOk();
        $logoutResponse->assertJson([
            'message' => 'Logged out',
        ]);

        // Token has been deleted
        $this->assertCount(0, $user->fresh()->tokens);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;

class SecurityConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rate_limiting_throttles_excessive_login_attempts(): void
    {
        $user = User::factory()->create([
            'email' => 'ratelimit@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attempt 5 logins (the allowed limit)
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'email' => 'ratelimit@example.com',
                'password' => 'wrong-password',
                'device_name' => 'test-device',
            ]);
            $response->assertStatus(401);
        }

        // 6th attempt should be blocked by rate limiter with 429 Too Many Requests
        $blockedResponse = $this->postJson('/api/auth/login', [
            'email' => 'ratelimit@example.com',
            'password' => 'wrong-password',
            'device_name' => 'test-device',
        ]);

        $blockedResponse->assertStatus(429);
    }

    public function test_cors_configuration_allows_configured_origins(): void
    {
        $allowedOrigin = 'http://localhost:3000';

        $response = $this->withHeaders([
            'Origin' => $allowedOrigin,
            'Access-Control-Request-Method' => 'GET',
        ])->optionsJson('/api/products');

        $response->assertHeader('Access-Control-Allow-Origin', $allowedOrigin);
    }

    public function test_token_api_requests_do_not_require_csrf_token(): void
    {
        // Stateless API routes accept JSON POST without X-CSRF-TOKEN
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'secret',
            'device_name' => 'test',
        ]);

        // Returns 401 (invalid credentials), NOT 419 Page Expired / CSRF mismatch
        $response->assertStatus(401);
    }
}

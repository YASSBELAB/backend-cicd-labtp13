<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class ExceptionHandlingTest extends TestCase
{
    use RefreshDatabase;

    private User $normalUser;
    private User $otherUser;
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $userRole = Role::firstOrCreate(['name' => 'USER']);

        $this->normalUser = User::factory()->create();
        $this->normalUser->roles()->attach($userRole);

        $this->otherUser = User::factory()->create();
        $this->otherUser->roles()->attach($userRole);

        $this->client = Client::factory()->create();
    }

    public function test_unauthenticated_request_returns_401_with_custom_message(): void
    {
        $response = $this->getJson('/api/orders');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Non authentifié',
        ]);
    }

    public function test_unauthorized_action_returns_403_with_acces_refuse(): void
    {
        $otherClient = Client::factory()->create();
        // Ensure otherClient->id != normalUser->id
        while ($otherClient->id == $this->normalUser->id) {
            $otherClient = Client::factory()->create();
        }

        $otherOrder = Order::factory()->create([
            'user_id' => $this->otherUser->id,
            'client_id' => $otherClient->id,
        ]);

        // User has token abilities, but policy denies viewing other user's order
        Sanctum::actingAs($this->normalUser, ['order:view']);

        $response = $this->getJson("/api/orders/{$otherOrder->id}");

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Accès refusé',
        ]);
    }

    public function test_missing_token_ability_returns_403_with_permission_manquante(): void
    {
        // User has no order:create ability
        Sanctum::actingAs($this->normalUser, ['order:view']);

        $response = $this->postJson('/api/orders', [
            'client_id' => $this->client->id,
            'items' => [
                ['product_id' => 1, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Permission manquante',
        ]);
    }
}
